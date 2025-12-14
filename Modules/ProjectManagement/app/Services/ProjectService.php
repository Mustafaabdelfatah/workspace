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

class ProjectService
{
    public function createProject(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $workspace = $this->validateWorkspace($data['workspace_id']);
                if (!$workspace['success']) {
                    return $workspace;
                }

                $project = $this->createOrUpdateProject($data);
                $this->processProjectInvitations($project, $data);

                return [
                    'success' => true,
                    'message' => empty($data['project_id']) ? 'Project created successfully' : 'Project updated successfully',
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

    private function createOrUpdateProject(array $data): Project
    {
        $projectData = [
            'workspace_id' => $data['workspace_id'],
            'name' => $data['name'],
            'owner_id' => auth()->id(),
            'entity_type' => $data['entity_type'] ?? null,
            'project_type' => $data['project_type'] ?? null,
            'custom_project_type' => $data['custom_project_type'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ];

        if (isset($data['project_type'])) {
            $workspace = Workspace::find($data['workspace_id']);
            $projectData['workspace_details_completed'] = $this->isWorkspaceComplete($workspace);

            if (!$projectData['workspace_details_completed']) {
                throw new \Exception('Workspace details must be completed before selecting project type');
            }
        }

        if (!empty($data['project_id'])) {
            $project = Project::where('id', $data['project_id'])
                            ->where('owner_id', auth()->id())
                            ->firstOrFail();
            $project->update($projectData);
            return $project;
        }

        $projectData['code'] = Project::generateCode($data['workspace_id']);

        return Project::updateOrCreate(
            [
                'workspace_id' => $data['workspace_id'],
                'owner_id' => auth()->id(),
            ],
            $projectData
        );
    }

    private function processProjectInvitations(Project $project, array $data): void
    {
        if (empty($data['invitations']) || !is_array($data['invitations'])) {
            return;
        }

        foreach ($data['invitations'] as $invitation) {
            $this->createOrUpdateProjectInvitation($project, $invitation);
        }
    }

    private function createOrUpdateProjectInvitation(Project $project, array $invitation): void
    {
        $role = $invitation['role'] ?? 'member';
        $message = $invitation['message'] ?? null;

        if (!empty($invitation['user_id'])) {
            $user = User::find($invitation['user_id']);
            if (!$user) {
                throw new \Exception("User with ID {$invitation['user_id']} not found");
            }

            // Check if invitation already exists for this user (using correct column name)
            $existingInvitation = ProjectInvitation::where('project_id', $project->id)
                                                 ->where('invited_user_id', $invitation['user_id'])
                                                 ->first();

            if (!$existingInvitation) {
                $project->inviteUser($user, $role, $message);
            }
        }

        if (!empty($invitation['group_id'])) {
            $group = UserGroup::find($invitation['group_id']);
            if (!$group) {
                throw new \Exception("Group with ID {$invitation['group_id']} not found");
            }

            // Check if invitation already exists for this group
            $existingInvitation = ProjectInvitation::where('project_id', $project->id)
                                                 ->where('user_group_id', $invitation['group_id'])
                                                 ->first();

            if (!$existingInvitation) {
                $project->inviteUserGroup($group, $role, $message);
            }
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

        // Check if workspace has a name in at least one language
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

    public function updateProject(int $id, array $data): Project
    {
        $project = Project::findOrFail($id);

        // Check if user has permission to update this project
        if ($project->owner_id !== auth()->id()) {
            throw new \Exception('You do not have permission to update this project');
        }

        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['entity_type'])) {
            $updateData['entity_type'] = $data['entity_type'];
        }

        if (isset($data['project_type'])) {
            $updateData['project_type'] = $data['project_type'];
        }

        if (isset($data['custom_project_type'])) {
            $updateData['custom_project_type'] = $data['custom_project_type'];
        }

        if (isset($data['start_date'])) {
            $updateData['start_date'] = $data['start_date'];
        }

        if (isset($data['end_date'])) {
            $updateData['end_date'] = $data['end_date'];
        }

        if (isset($data['manager_id'])) {
            $updateData['manager_id'] = $data['manager_id'];
        }

        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }

        if (isset($data['project_type'])) {
            $workspace = Workspace::find($project->workspace_id);
            $updateData['workspace_details_completed'] = $this->isWorkspaceComplete($workspace);

            if (!$updateData['workspace_details_completed']) {
                throw new \Exception('Workspace details must be completed before selecting project type');
            }
        }

        $project->update($updateData);

        if (isset($data['invitations']) && is_array($data['invitations'])) {
            $this->processProjectInvitations($project, $data);
        }

        return $project->load(['workspace', 'owner', 'manager', 'invitations.invitedUser', 'invitations.userGroup']);
    }

    public function changeProjectStatus(int $projectId, string $status): Project
    {
        $project = Project::findOrFail($projectId);

        // Check if user has permission to change this project's status
        if ($project->owner_id !== auth()->id()) {
            throw new \Exception('You do not have permission to change this project status');
        }

        // Update the project status
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

    public function getFilteredProjects(array $filters, int $perPage = 20, int $page = 1)
    {

        $query = Project::query()->with(['owner', 'manager', 'workspace']);

        if (isset($filters['workspace_id'])) {
            $query->where('workspace_id', $filters['workspace_id']);
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['project_type']) && !empty($filters['project_type'])) {
            $query->where('project_type', $filters['project_type']);
        }

        if (isset($filters['entity_type']) && !empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $searchTerm = $filters['search'];
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%");
            });
        }

        if (isset($filters['owner_only']) && $filters['owner_only']) {
            $query->where('owner_id', auth()->id());
        }

        // Apply ordering
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}