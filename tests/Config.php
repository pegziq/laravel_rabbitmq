<?php
return  [
    'host' => '120.25.95.169',
    'port' => '5672',
    'login' => 'devtest',
    'password' => 'DJDGjhjr1708',
    'vhost' => 'test',
    'queue_bind'=>[
        'loan'=>[
            'exchange' => 'tender',
            'routing_key' => 'loan.*',
            'callback' => 'Action'
        ]
    ],
    'exchange_bind'=>[
        'tender'=>[
            'exchange_type' => 'direct'
        ]
    ]
];