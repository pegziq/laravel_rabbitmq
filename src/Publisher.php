<?php

namespace Pegziq\LaravelRabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

class Publisher extends RabbitMQ
{
    public function publish($routing, $exchange, $message, $config = [])
    {
        $message = new AMQPMessage($message, [
            'delivery_mode' => isset($config['delivery_mode']) ? $config['delivery_mode'] : 2
        ]);
        $this->channel($exchange);
        return $this->channel->basic_publish($message, $exchange, $routing);
    }
}
