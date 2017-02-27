<?php

return [
    'triggers' => [
        'exchange' => 'marketing', 'exchange_type' => 'topic', 'routing_key' => 'trigger.#', 'callback' => 'App\Queues\Triggers'
    ],
    'message' => [
        'exchange' => 'immediate_job'
    ]
];
