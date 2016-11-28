<?php
namespace Pegziq\LaravelRabbitMQ;

abstract class AbstractQueue
{
    public $delivery_info;
    public $channel;
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
        $this->delivery_info = $message->delivery_info;
        $this->channel = $message->delivery_info['channel'];
    }

    public function ack()
    {
        $this->channel->basic_ack($this->delivery_info['delivery_tag']);
        if ($this->message->body === 'quit') {
            $this->delivery_info['channel']->basic_cancel($this->delivery_info['consumer_tag']);
        }
        return true;
    }

    public function reject($requeue = false)
    {
        $this->channel->basic_reject($this->delivery_info['delivery_tag'], $requeue);
        return true;
    }

}
