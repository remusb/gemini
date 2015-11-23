<?php

return [

    'default' => 'mongodb',

    'connections' => [

        'mongodb' => array(
            'driver'   => 'mongodb',
            'host'     => '192.168.0.11',
            'port'     => 27017,
            'username' => 'gemini',
            'password' => 'gemini',
            'database' => 'gemini'
        ),

    ],

    'migrations' => 'migrations',
];