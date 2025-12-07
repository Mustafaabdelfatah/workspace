<?php

namespace Modules\ProjectManagement\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\ProjectManagement\App\Enums\ProjectStatusEnum;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled in the service layer
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|array',
            'name.en' => 'sometimes|required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'status' => 'nullable|string|in:' . implode(',', array_column(ProjectStatusEnum::cases(), 'value')),
            'project_type' => 'nullable|string|in:' . implode(',', array_column(ProjectTypeEnum::cases(), 'value')),
            'building_type' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'parent_project_id' => 'nullable|exists:projects,id',
            // 'company_id' => 'nullable|exists:companies,id', // Uncomment when company module is ready
            // 'company_position_id' => 'nullable|exists:company_positions,id', // Uncomment when company module is ready
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'area' => 'nullable|numeric|min:0',
            'area_unit' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Project name is required',
            'name.max' => 'Project name must not exceed 255 characters',
            'manager_id.exists' => 'Selected manager does not exist',
            'parent_project_id.exists' => 'Selected parent project does not exist',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'latitude.between' => 'Latitude must be between -90 and 90',
            'longitude.between' => 'Longitude must be between -180 and 180',
            'area.min' => 'Area must be a positive number',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'manager_id' => 'manager',
            'parent_project_id' => 'parent project',
            'project_type' => 'project type',
            'building_type' => 'building type',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'area_unit' => 'area unit',
        ];
    }
}
