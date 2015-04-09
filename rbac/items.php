<?php
return [
    'adminDashboard' => [
        'type' => 2,
    ],
    'viewProject' => [
        'type' => 2,
        'description' => 'View project',
        'ruleName' => 'isCreator',
    ],
    'user' => [
        'type' => 1,
        'description' => 'Пользователь',
        'children' => [
            'viewProject',
        ],
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
