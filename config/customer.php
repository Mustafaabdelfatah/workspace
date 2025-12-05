<?php

return [
    'name' => 'Customer',
    'database_connection' => 'customer',
    'module_prefix' => 'Modules\\Customer\\Models\\',
    'quotation_types' => [
        'manual',
        'almnaber',
    ],
    'opportunity_status' => [
        'draft',
        'pending',
        'active'
    ],
];
