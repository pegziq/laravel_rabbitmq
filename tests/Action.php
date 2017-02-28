<?php
namespace Pegziq\LaravelRabbitMQ\Tests;

use Pegziq\LaravelRabbitMQ\AbstractQueue;

class Action extends AbstractQueue
{
    public function fire()
    {
        $data = json_decode($this->message->body, true) ?? $this->message->body;
        try {
            $this->message->delivery_info['channel']->basic_cancel($this->message->delivery_info['consumer_tag']);
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
