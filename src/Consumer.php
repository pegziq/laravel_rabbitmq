<?php

namespace Pegziq\LaravelRabbitMQ;


class Consumer extends RabbitMQ
{
    public function consume($queue,$callback){
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queue, '', false, false, false, false, $callback);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
        return $this->channel->close();
    }
}
