<?php

return [
    'length' => 6,

    'max_tries' => 3,

    'http' => [
        'path' => 'magictoken',

        'input_keys' => [
            'token' => 'token',
            'pincode' => 'pincode',
        ]
    ],

    'database' => [
        'expires' => 5,

        'table_name' => 'magic_tokens',
    ]
];
