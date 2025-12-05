<?php

return [
    'default_validation_string_min' => 3,
    'default_validation_string_max' => 255,
    'invoices_issuing_types' => [
        '1100',  // 1100 for together
        '0100',  // 0100 for simplified
        '1000'  // 1000 for standard
    ],
    'zatca_stages' => [
        'developer-portal',
        'simulation',
        'core',
    ],
];
