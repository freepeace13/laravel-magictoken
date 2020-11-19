<?php

return [
    'code_length' => 6,

    'http_requests' => [
        'path' => 'magictoken',

        'form_inputs' => [
            'token' => 'input_token',
            'pincode' => 'input_pincode',
        ]
    ],

    'database' => [
        'table_name' => 'magic_tokens'
    ]
];
