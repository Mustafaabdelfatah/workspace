<?php

namespace Modules\ProjectManagement\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => 'nullable|exists:workspaces,id',
            'status' => 'nullable|string',
            'project_type' => 'nullable|string',
            'entity_type' => 'nullable|string|in:contractor,consultant,developer',
            'search' => 'nullable|string|max:255',
            'owner_id' => 'nullable|exists:users,id',
            'owner_only' => 'nullable|boolean',
            'manager_id' => 'nullable|exists:users,id',
            'custom_project_type' => 'nullable|string|max:255',
            'start_date_from' => 'nullable|date',
            'start_date_to' => 'nullable|date',
            'end_date_from' => 'nullable|date',
            'end_date_to' => 'nullable|date',
            'workspace_details_completed' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'order_by' => 'nullable|string|in:created_at,updated_at,name,code,start_date,end_date',
            'order_dir' => 'nullable|string|in:asc,desc'
        ];
    }
}
