<?php

namespace Modules\ProjectManagement\App\Services;

use Modules\Core\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\UserGroup;
use Modules\Core\Models\Workspace;
use Modules\ProjectManagement\App\Models\Project;
use Modules\ProjectManagement\App\Enums\UserTypeEnum;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;
use Modules\ProjectManagement\App\Models\ProjectInvitation;
use Modules\ProjectManagement\App\Http\Requests\CreateProjectRequest;
use Modules\ProjectManagement\App\Http\Requests\ProjectFilterRequest;
use Modules\ProjectManagement\App\Filters\ProjectFilterPipeline;
use Modules\Core\Repositories\InvitationRepository;

class ProjectService
{
    protected $filterPipeline;
    protected $invitationRepository;

    public function __construct(ProjectFilterPipeline $filterPipeline, InvitationRepository $invitationRepository)
    {
        $this->filterPipeline = $filterPipeline;
        $this->invitationRepository = $invitationRepository;
    }

    public function createProject(CreateProjectRequest $request): array
    {
        try {
            return DB::transaction(function () use ($request) {
                $workspace = $this->validateWorkspace($request->workspace_id);
                if (!$workspace['success']) {
                    return $workspace;
                }

                $project = $this->createOrUpdateProject($request);
                $this->processProjectInvitations($project, $request);

                return [
                    'success' => true,
                    'message' => empty($request->project_id) ? 'Project created successfully' : 'Project updated successfully',
                    'project' => $project->load(['workspace', 'owner', 'invitations.invitedUser', 'invitations.userGroup'])
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function validateWorkspace(int $workspaceId): array
    {
        $workspace = Workspace::find($workspaceId);

        if (!$workspace) {
            return [
                'success' => false,
                'message' => 'Workspace not found'
            ];
        }

        if (!$this->isWorkspaceComplete($workspace)) {
            return [
                'success' => false,
                'message' => 'Please complete your workspace details before creating a project',
                'workspace_incomplete' => true
            ];
        }

        return [
            'success' => true,
            'workspace' => $workspace
        ];
    }

    private function createOrUpdateProject(CreateProjectRequest $request): Project
    {
        $projectData = $request->only([
            'workspace_id', 'name', 'entity_type', 'project_type',
            'custom_project_type', 'start_date', 'end_date'
        ]);

        $projectData['owner_id'] = auth()->id();

        if ($request->filled('project_type')) {
            $workspace = Workspace::find($request->workspace_id);
            $projectData['workspace_details_completed'] = $this->isWorkspaceComplete($workspace);

            if (!$projectData['workspace_details_completed']) {
                throw new \Exception('Workspace details must be completed before selecting project type');
            }
        }

        if ($request->filled('project_id')) {
            $project = Project::where('id', $request->project_id)
                            ->where('owner_id', auth()->id())
                            ->firstOrFail();
            $project->update($projectData);
            return $project;
        }

        $projectData['code'] = Project::generateCode($request->workspace_id);

        return Project::updateOrCreate(
            [
                'workspace_id' => $request->workspace_id,
                'owner_id' => auth()->id(),
            ],
            $projectData
        );
    }

    private function processProjectInvitations(Project $project, CreateProjectRequest $request): void
    {
        if (!$request->has('invitations') || !is_array($request->invitations)) {
            return;
        }

        $emails = [];
        $invitationItems = [];

        foreach ($request->invitations as $invitation) {
             if (!empty($invitation['user_group_id'])) {
                 if (!empty($invitation['emails']) && is_array($invitation['emails'])) {
                    foreach ($invitation['emails'] as $email) {
                        $emails[] = $email;

                        $invitationItems[] = [
                            'scope_type' => 'project',
                            'scope_id' => $project->id,
                            'user_group_id' => $invitation['user_group_id']
                        ];
                    }
                } else {
                     $userGroup = UserGroup::find($invitation['user_group_id']);
                    if ($userGroup) {
                        $groupUsers = User::whereHas('userGroups', function($query) use ($invitation) {
                            $query->where('user_group_id', $invitation['user_group_id']);
                        })->get();

                        foreach ($groupUsers as $user) {
                            $emails[] = $user->email;

                            $invitationItems[] = [
                                'scope_type' => 'project',
                                'scope_id' => $project->id,
                                'user_group_id' => $invitation['user_group_id']
                            ];
                        }
                    }
                }
            }

             if (!empty($invitation['email'])) {
                $emails[] = $invitation['email'];

                $invitationItems[] = [
                    'scope_type' => 'project',
                    'scope_id' => $project->id,
                    'role_id' => $invitation['role_id'] ?? null
                ];
            }
        }

        if (!empty($emails)) {
            $invitationArgs = [
                'workspace_id' => $project->workspace_id,
                'emails' => array_unique($emails),
                'items' => $invitationItems
            ];

            $this->invitationRepository->sendInvitation($invitationArgs);
        }
    }

    private function isWorkspaceComplete(Workspace $workspace): bool
    {
        $requiredFields = ['name', 'logo_path', 'workspace_type'];

        foreach ($requiredFields as $field) {
            if (empty($workspace->$field)) {
                return false;
            }
        }

        if ($workspace->workspace_type === 'official' || $workspace->workspace_type === 'company') {
            $officialFields = ['a4_official_path', 'stamp_path'];
            foreach ($officialFields as $field) {
                if (empty($workspace->$field)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getWorkspaceMissingFields(Workspace $workspace): array
    {
        $requiredFields = [
            'logo_path' => 'Logo',
            'a4_official_path' => 'Official A4 Template',
            'stamp_path' => 'Official Stamp'
        ];

        $missing = [];

        $hasName = false;
        if ($workspace->name) {
            if (is_array($workspace->name)) {
                $hasName = !empty($workspace->name['en']) || !empty($workspace->name['ar']);
            } else {
                $hasName = !empty($workspace->name);
            }
        }

        if (!$hasName) {
            $missing[] = 'Workspace Name';
        }

        foreach ($requiredFields as $field => $label) {
            if (empty($workspace->$field)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    public function getWorkspaceStatus(int $workspaceId): array
    {
        $workspace = Workspace::find($workspaceId);

        if (!$workspace) {
            return [
                'success' => false,
                'message' => 'Workspace not found'
            ];
        }

        return [
            'success' => true,
            'workspace' => $workspace,
            'is_complete' => $this->isWorkspaceComplete($workspace),
            'missing_fields' => $this->getWorkspaceMissingFields($workspace)
        ];
    }

    public function getProjectById(int $id, bool $detailed = false): ?Project
    {
        $query = Project::with(['workspace', 'owner', 'manager']);

        if ($detailed) {
            $query->with(['invitations.invitedUser', 'invitations.userGroup']);
        }

        return $query->find($id);
    }

    public function updateProject(int $id, CreateProjectRequest $request): Project
    {
        $project = Project::findOrFail($id);

        if ($project->owner_id !== auth()->id()) {
            throw new \Exception('You do not have permission to update this project');
        }

        $updateData = $request->only([
            'name', 'entity_type', 'project_type', 'custom_project_type',
            'start_date', 'end_date', 'manager_id', 'status'
        ]);

        if ($request->filled('project_type')) {
            $workspace = Workspace::find($project->workspace_id);
            $updateData['workspace_details_completed'] = $this->isWorkspaceComplete($workspace);

            if (!$updateData['workspace_details_completed']) {
                throw new \Exception('Workspace details must be completed before selecting project type');
            }
        }

        $project->update(array_filter($updateData, fn($value) => $value !== null));

        if ($request->has('invitations') && is_array($request->invitations)) {
            $this->processProjectInvitations($project, $request);
        }

        return $project->load(['workspace', 'owner', 'manager', 'invitations.invitedUser', 'invitations.userGroup']);
    }

    public function changeProjectStatus(int $projectId, string $status): Project
    {
        $project = Project::findOrFail($projectId);

        if ($project->owner_id !== auth()->id()) {
            throw new \Exception('You do not have permission to change this project status');
        }

        $project->update(['status' => $status]);

        return $project->load(['workspace', 'owner', 'manager', 'invitations.invitedUser', 'invitations.userGroup']);
    }

    public function getUserTypes(): array
    {
        return array_map(function($type) {
            return [
                'value' => $type->value,
                'label' => $type->label()
            ];
        }, UserTypeEnum::cases());
    }

    public function getProjectTypes(): array
    {
        return array_map(function($type) {
            return [
                'value' => $type->value,
                'label' => $type->label()
            ];
        }, ProjectTypeEnum::cases());
    }

    public function getFilteredProjects(ProjectFilterRequest $request)
    {
        $perPage = $request->per_page ?? 20;
        $page = $request->page ?? 1;
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';

        $query = Project::query()->with(['owner', 'manager', 'workspace']);

        $query = $this->filterPipeline->apply($query, $request->validated());

        $query->orderBy($orderBy, $orderDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
