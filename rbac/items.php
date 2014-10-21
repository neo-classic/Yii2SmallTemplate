<?php
return [
    'adminDashboard' => [
        'type' => 2,
    ],
    'user' => [
        'type' => 1,
        'description' => 'Пользователь',
        'ruleName' => 'userRole',
    ],
    'client' => [
        'type' => 1,
        'description' => 'Клиент',
        'ruleName' => 'userRole',
        'children' => [
            'user',
        ],
    ],
    'manager' => [
        'type' => 1,
        'description' => 'Менеджер',
        'ruleName' => 'userRole',
        'children' => [
            'client',
            'adminDashboard',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Админ',
        'ruleName' => 'userRole',
        'children' => [
            'manager',
        ],
    ],
];
