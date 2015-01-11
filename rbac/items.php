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
        'description' => 'User',
        'children' => [
            'viewProject',
        ],
    ],
    'client' => [
        'type' => 1,
        'description' => 'Client',
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
        'description' => 'Admin',
        'children' => [
            'manager',
            'adminDashboard',
        ],
    ],
];
