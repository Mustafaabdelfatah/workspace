<?php

namespace Modules\ProjectManagement\App\Traits\GraphQL;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait GraphQLValidationTrait
{
    /**
     * Validate input data using request class rules
     */
    protected function validateInput(array $input, string $requestClass): ?array
    {
        $request = new $requestClass();
        $validator = Validator::make(
            $input,
            $request->rules(),
            $request->messages() ?? [],
            $request->attributes() ?? []
        );

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed: ' . $validator->errors()->first());
        }

        return null;
    }

    /**
     * Validate input with custom rules
     */
    protected function validateWithRules(array $input, array $rules, array $messages = []): ?array
    {
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed: ' . $validator->errors()->first());
        }

        return null;
    }

    /**
     * Get validation rules for common project operations
     */
    protected function getProjectIdValidationRules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:projects,id']
        ];
    }

    /**
     * Get validation rules for workspace operations
     */
    protected function getWorkspaceIdValidationRules(): array
    {
        return [
            'workspace_id' => ['required', 'integer', 'exists:workspaces,id']
        ];
    }

    /**
     * Get validation rules for project status
     */
    protected function getProjectStatusValidationRules(): array
    {
        return [
            'status' => ['required', 'string', 'in:planning,active,on_hold,completed,cancelled']
        ];
    }
}
