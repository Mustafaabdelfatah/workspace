<?php
return [
    'name' => 'ProjectManagement',
    'database_connection' => config('core.database_connection', 'mysql'),

    'graphql' => require __DIR__ . '/graphql.php',

    'projects' => [
        'code_prefix' => 'PRJ-',
        'default_status' => \Modules\ProjectManagement\App\Enums\ProjectStatusEnum::PLANNING,
    ],
];
