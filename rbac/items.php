<?php
return [
    'adminDashboard' => [
        'type' => 2,
    ],
    'user' => [
        'type' => 1,
        'description' => 'Пользователь',
    ],
    'client' => [
        'type' => 1,
        'description' => 'Клиент',
        'children' => [
            'user',
        ],
    ],
    'manager' => [
        'type' => 1,
        'description' => 'Manager',
        'children' => [
            'client',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Админ',
        'children' => [
            'manager',
            'adminDashboard',
        ],
    ],
];
