<?php

namespace Pegziq\LaravelRabbitMQ;


class Consumer extends RabbitMQ
{
    public $queue_bind;
    public $callback;

    public function consume($queue_name, $callback)
    {
        $this->channel(null,$queue_name);
        $this->channel->queue_declare($queue_name, false, true, false, false);
        $this->channel->queue_bind($queue_name, $this->exchange_name, $this->routing_key);
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queue_name, '', false, false, false, false, $callback);
        register_shutdown_function([$this, "shutdown"]);
        try {
            while (count($this->channel->callbacks)) {
                $this->channel->wait();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function shutdown()
    {
        $this->channel_close();
        $this->connect_close();
    }
}
