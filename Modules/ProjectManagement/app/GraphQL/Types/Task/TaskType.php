<?php

namespace Modules\ProjectManagement\App\GraphQL\Types\Task;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TaskType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Task',
        'description' => 'A task within a project'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The task ID'
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The task title'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The task description'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'The task status'
            ],
            'priority' => [
                'type' => Type::string(),
                'description' => 'The task priority'
            ],
            'start_date' => [
                'type' => Type::string(),
                'description' => 'The task start date',
                'resolve' => function($task) {
                    return $task->start_date?->format('Y-m-d');
                }
            ],
            'end_date' => [
                'type' => Type::string(),
                'description' => 'The task end date',
                'resolve' => function($task) {
                    return $task->end_date?->format('Y-m-d');
                }
            ],
            'project_id' => [
                'type' => Type::int(),
                'description' => 'The project ID this task belongs to'
            ],
            'assignee_id' => [
                'type' => Type::int(),
                'description' => 'The user ID assigned to this task'
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The task creation timestamp',
                'resolve' => function($task) {
                    return $task->created_at->format('Y-m-d H:i:s');
                }
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The task update timestamp',
                'resolve' => function($task) {
                    return $task->updated_at->format('Y-m-d H:i:s');
                }
            ],
        ];
    }
}
