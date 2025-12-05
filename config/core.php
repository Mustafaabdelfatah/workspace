<?php

return [
    'name' => 'Core',
    'database_connection' => 'core',
    'otp_bearers' => [
        'mobile',
        'email',
        'whatsapp',
    ],
    'otp_expire_seconds' => 120,
    'otp_length' => 4,
    'otp_action_types' => [
        'register',
        'login',
        'verify',
    ],
];