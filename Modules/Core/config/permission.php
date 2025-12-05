<?php

return [
    'query' => [
        'permissionsTree' => Modules\Core\app\GraphQL\Queries\Permission\PermissionsTreeQuery::class,
    ],
    'mutation' => [

    ],
    'type' => [
        'PermissionsTree' =>  Modules\Core\app\GraphQL\Types\Permission\PermissionsTreeType::class,

    ]
];

