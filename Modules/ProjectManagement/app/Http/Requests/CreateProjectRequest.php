<?php

namespace Modules\ProjectManagement\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;

class CreateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'project_id' => 'sometimes|exists:projects,id',
            'workspace_id' => 'required|exists:workspaces,id',
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'entity_type' => 'nullable|string|in:contractor,consultant,developer',
            'project_type' => 'nullable|string|in:' . implode(',', array_column(ProjectTypeEnum::cases(), 'value')),
            'custom_project_type' => 'nullable|string|max:255|required_if:project_type,other',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'invitations' => 'nullable|array',
            'invitations.*.user_id' => 'nullable|exists:users,id',
            'invitations.*.group_id' => 'nullable|exists:user_groups,id',
            'invitations.*.role' => 'nullable|string|in:member,admin,viewer',
            'invitations.*.message' => 'nullable|string|max:500'
        ];

        if (!$this->filled('project_id')) {
            $rules['name'] = [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (!is_array($value)) {
                        $fail('The name must be an array with language keys.');
                        return;
                    }

                    $workspaceId = $this->input('workspace_id');
                    if (!$workspaceId) {
                        return;
                    }

                    $nameEn = $value['en'] ?? '';
                    $nameAr = $value['ar'] ?? '';

                    if (empty($nameEn) && empty($nameAr)) {
                        return;
                    }

                    $existingProject = \Modules\ProjectManagement\App\Models\Project::where('workspace_id', $workspaceId)
                        ->where(function($query) use ($nameEn, $nameAr) {
                            if (!empty($nameEn)) {
                                $query->whereRaw("JSON_EXTRACT(name, '$.en') = ?", [$nameEn]);
                            }
                            if (!empty($nameAr)) {
                                $query->orWhereRaw("JSON_EXTRACT(name, '$.ar') = ?", [$nameAr]);
                            }
                        })
                        ->first();

                    if ($existingProject) {
                        $fail('A project with this name already exists in the workspace.');
                    }
                }
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'workspace_id.required' => 'Workspace is required',
            'name.en.required' => 'English name is required',
            'name.ar.required' => 'Arabic name is required',
            'custom_project_type.required_if' => 'Custom project type is required when selecting "Other"',
            'end_date.after_or_equal' => 'End date must be after or equal to start date'
        ];
    }
}
