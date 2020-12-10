<?php

return [
    'length' => 6,

    'max_tries' => 3,

    'path' => 'magictoken',

    'view' => 'magictoken::verify',

    'database' => [
        'table_name' => 'magic_tokens',

        'expires' => 5,
    ]
];
