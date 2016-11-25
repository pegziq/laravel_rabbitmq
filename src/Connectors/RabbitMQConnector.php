<?php
namespace Pegziq\LaravelRabbitMQ\Connectors;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConnector
{

    public $AMQPConnection;
    protected $channel;


    public function connect(array $config)
    {
        $this->AMQPConnection = new AMQPStreamConnection($config['host'], $config['port'], $config['login'], $config['password'], $config['vhost']);
        return $this->AMQPConnection;
    }

    public function open($args, $config=[]){
        if(!$this->AMQPConnection){
            $this->connect($config);
        }
        $this->channel = $this->AMQPConnection->channel();
        $this->channel->queue_declare($args['queue'], false, true, false, false);
        $this->channel->exchange_declare($args['exchange'], $args['exchange_type'], false, true, false);
        $this->channel->queue_bind($args['queue'], $args['exchange'], $args['routing_key']);
        return $this->channel;
    }

    public function close(){
        $this->AMQPConnection->close();
    }
}