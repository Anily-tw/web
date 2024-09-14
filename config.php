<?php
return [
    'credentials' => [
        'user1' => 'pass1',
        'user2' => 'pass2',
        'admin' => 'passAdmin',
    ],
    'permissions' => [
        'admin' => ['anime', 'souly', 'other', 'joni'],
        'user1' => ['anime'],
        'user2' => ['other'],
    ],
    'uploadDir' => 'path/to/maps',
    'scriptDir' => 'path/to/add_map.py'
];
