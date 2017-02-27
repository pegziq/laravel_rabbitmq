<?php
namespace App\Queues;

use Pegziq\LaravelRabbitMQ\AbstractQueue;

class Action extends AbstractQueue
{
    public function fire()
    {
        $data = json_decode($this->message->body, true);
        try {
            //do something...
            return $this->ack();
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
