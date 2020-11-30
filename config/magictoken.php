<?php

return [
    'length' => 6,

    'max_tries' => 3,

    'http' => [
        'path' => 'magictoken',

        'access_form_view' => 'magictoken::verify',

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
