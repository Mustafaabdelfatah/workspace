<?php
return [
    'auth' => [
        'query' => [
            'projects' => \Modules\ProjectManagement\App\GraphQL\Queries\Project\GetProjectsQuery::class,
            'project' => \Modules\ProjectManagement\App\GraphQL\Queries\Project\GetProjectByIdQuery::class,
            'projectStats' => \Modules\ProjectManagement\App\GraphQL\Queries\Project\GetProjectStatsQuery::class,
        ],
        'mutation' => [
            'createProject' => \Modules\ProjectManagement\App\GraphQL\Mutations\Project\CreateProjectMutation::class,
            'updateProject' => \Modules\ProjectManagement\App\GraphQL\Mutations\Project\UpdateProjectMutation::class,
            'deleteProject' => \Modules\ProjectManagement\App\GraphQL\Mutations\Project\DeleteProjectMutation::class,
            'changeProjectStatus' => \Modules\ProjectManagement\App\GraphQL\Mutations\Project\ChangeProjectStatusMutation::class,
        ],
        'type' => [
            \Modules\ProjectManagement\App\GraphQL\Types\Project\ProjectType::class,
            \Modules\ProjectManagement\App\GraphQL\Types\Project\ProjectsResponseType::class,
            \Modules\ProjectManagement\App\GraphQL\Types\Project\ProjectSingleResponseType::class,
            \Modules\ProjectManagement\App\GraphQL\Types\Project\ProjectStatsType::class,
            \Modules\ProjectManagement\App\GraphQL\Types\Task\TaskType::class,
            \Modules\ProjectManagement\App\GraphQL\Inputs\ProjectInput::class,
            \Modules\ProjectManagement\App\GraphQL\Inputs\ProjectUpdateInput::class,
            \Modules\ProjectManagement\App\GraphQL\Inputs\ProjectFilterInput::class,
            \Modules\ProjectManagement\App\GraphQL\Types\Enums\ProjectStatusEnumType::class,
        ]
    ],
];
