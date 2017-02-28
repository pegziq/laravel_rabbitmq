<?php
namespace Pegziq\LaravelRabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

class Publisher extends RabbitMQ
{
    public function publish($message, $exchange, $routing, $config = [])
    {
        $this->channel($exchange);
        $this->durable && $config['delivery_mode'] = 2;
        $message = new AMQPMessage($message, $config);
        return $this->channel->basic_publish($message, $exchange, $routing);
    }
}
