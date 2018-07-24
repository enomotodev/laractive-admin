<?php

return [
    'title' => 'Admin',
    'route_prefix' => 'admin',
    'httpauth' => [
        'enable' => false,
        'type' => 'basic',
        'realm' => 'Secured',
        'username' => 'admin',
        'password' => 'password',
    ],
];
