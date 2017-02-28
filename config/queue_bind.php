<?php

return [
    'queue_bind' => [
        'loan' => [
            'exchange' => 'tender',
            'routing_key' => 'loan.*',
            'callback' => 'Action'
        ]
    ],
    'exchange_bind' => [
        'tender' => [
            'exchange_type' => 'direct'
        ]
    ]
];
