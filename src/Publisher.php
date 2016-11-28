<?php

namespace Pegziq\LaravelRabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

class Publisher extends RabbitMQ
{
    public function publish($routing, $message){
        $message = new AMQPMessage($message,[
            'delivery_mode'=>$this->args['delivery_mode'] ?? 2
        ]);
        $this->channel->basic_publish($message, $this->args['exchange'], $routing);
    }
}
