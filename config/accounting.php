<?php

return [
    'name' => 'Accounting',
    'database_connection' => 'accounting',
    'module_prefix' => 'Modules\\Accounting\\Models\\',
    'accounts_settings_keys' => [
        'transfers_sides',
        'accounts_types',
    ],
    'transfers_sides' => [
        'not_have',
        'budget',
        'income_statement',
        'liabilities',
    ],
    'accounts_types' => [
        'assets',
        'equity',
        'liabilities',
        'revenues',
        'expenses',
        'profit_and_loss',
    ],
    'zatca_codes' => [
        'E' => ['VATEX-SA-29', 'VATEX-SA-29-7', 'VATEX-SA-30'],
        'Z' => ['VATEX-SA-32', 'VATEX-SA-33', 'VATEX-SA-34-1', 'VATEX-SA-34-2', 'VATEX-SA-34-3', 'VATEX-SA-34-4', 'VATEX-SA-34-5', 'VATEX-SA-35', 'VATEX-SA-36', 'VATEX-SA-EDU', 'VATEX-SA-HEA', 'VATEX-SA-MLTRY'],
        'O' => ['VATEX-SA-OOS'],
        'S' => []
    ]
];
