<?php

namespace Modules\ProjectManagement\App\Services;

use Modules\Core\Models\Workspace;
use Modules\Core\Models\User;
use Modules\Core\Models\UserGroup;
use Modules\ProjectManagement\App\Models\Project;
use Modules\ProjectManagement\App\Models\ProjectInvitation;
use Modules\ProjectManagement\App\Enums\UserTypeEnum;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;

class ProjectService
{
    public function createProject(array $data): array
    {
        try {
            return \DB::transaction(function () use ($data) {
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
            'user_type' => $data['user_type'] ?? null,
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
        $requiredFields = [ 'logo_path', 'a4_official_path', 'stamp_path'];
        foreach ($requiredFields as $field) {
            if (empty($workspace->$field)) {
                return false;
            }
        }

        return true;
    }

    private function getWorkspaceMissingFields(Workspace $workspace): array
    {
        $requiredFields = [
            'name' => 'Workspace Name',
            'logo_path' => 'Logo',
            'a4_official_path' => 'Official A4 Template',
            'stamp_path' => 'Official Stamp'
        ];

        $missing = [];

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
}
