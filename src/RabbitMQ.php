<?php

namespace Pegziq\LaravelRabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQ
{
    public $connect;
    public $channel;
    protected $args;
    protected $queue_bind;
    protected $rabbitMQConnector;

    public function init($config, $queue)
    {
        $this->connect = $this->connect($config);
        $this->channel = $this->declare($queue);
    }

    public function connect($config)
    {
        return new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['login'],
            $config['password'],
            $config['vhost'],
            $config['insist'] ?? false,
            $config['login_method'] ?? 'AMQPLAIN',
            $config['login_response'] ?? null,
            $config['locale'] ?? 'en_US',
            $config['connection_timeout'] ?? 120,
            $config['read_write_timeout'] ?? 120,
            $config['context'] ?? null,
            $config['keepalive'] ?? false,
            $config['heartbeat'] ?? 60
	);	
   }

    public function declare($queue)
    {
        if (!$this->validate($queue)) return false;
        $channel = $this->connect->channel();
        $channel->queue_declare($queue, $this->args['passive'], true, false, false);
        $channel->exchange_declare($this->args['exchange'], $this->args['exchange_type'], false, true, false);
        $channel->queue_bind($queue, $this->args['exchange'], $this->args['routing_key']);
        return $channel;
    }

    public function validate($queue)
    {
        $this->queue_bind = config('queue_bind');
        if (!isset($this->queue_bind[$queue]) || !$this->queue_bind[$queue]['callback']) {
            return false;
        }
        $this->args['exchange'] = $this->queue_bind[$queue]['exchange'] ?? false;
        $this->args['exchange_type'] = $this->queue_bind[$queue]['exchange_type'] ?? 'direct';
        $this->args['routing_key'] = $this->queue_bind[$queue]['routing_key'] ?? false;
        $this->args['callback'] = $this->queue_bind[$queue]['callback'] ?? false;
        $this->args['passive'] = $this->queue_bind[$queue]['passive'] ?? false;
        return true;
    }

    public function getParams($queue){
        $this->queue_bind = config('queue_bind');
        return $this->queue_bind[$queue];
    }

    public function acknowledge($message)
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        if ($message->body === 'quit') {
            $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        }
    }
}
