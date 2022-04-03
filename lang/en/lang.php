<?php

$name = 'Mandrill secret';

return [
    'plugin_description' => 'Mandrill mail driver plugin',

    'fields' => [
        'mandrill_secret' => [
            'label' => $name,
            'comment' => 'Enter your ' . $name,
        ],
    ],
];
