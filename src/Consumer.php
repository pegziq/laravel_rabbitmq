<?php

namespace Pegziq\LaravelRabbitMQ;


class Consumer extends RabbitMQ
{
    public function consume($queue, $callback)
    {
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queue, '', false, false, false, false, $callback);
        register_shutdown_function([$this, "shutdown"], $this->channel, $this->connect);
        try {
            while (count($this->channel->callbacks)) {
                $this->channel->wait();
            }
        } catch (Exception $e) {
            throw $e;
        }
        $this->channel->close();
        $this->connect->close();
        return true;
    }

    public function shutdown($channel, $connection)
    {
        $channel->close();
        $connection->close();
    }
}
