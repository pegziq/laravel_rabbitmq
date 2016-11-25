<?php
namespace Pegziq\LaravelRabbitMQ\Console;

use Illuminate\Console\Command;
use Pegziq\LaravelRabbitMQ\Connectors\RabbitMQConnector;
use Pegziq\LaravelRabbitMQ\RabbitMQListener;
use Pegziq\LaravelRabbitMQ\DeclareException;

class RabbitMQListenerCommand extends Command
{
    public $args;
    protected $signature = 'queue_rb:listen {connect?} {--queue=} {--sleep=} {--tries=} {--env=}';
    protected $description = 'rabbitMq监听';
    private $listener;
    private $channel;
    public $connection;
    private $rabbitMQConnector;

    public function __construct(RabbitMQListener $rabbitMQListener,
                                RabbitMQConnector $rabbitMQConnector
    )
    {
        parent::__construct();
        $this->listener = $rabbitMQListener;
        $this->rabbitMQConnector = $rabbitMQConnector;
    }

    public function fire()
    {
        $this->open();
        $callback = function($msg){
            echo " [x] Received ", $msg->body, "\n".rand();
            sleep(substr_count($msg->body, '.'));
            echo " [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->args['queue'], '', false, false, false, false, $callback);

        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
        $this->rabbitMQConnector->close();
    }

    public function open()
    {
        $this->validateOptions();
        try {
            $config = config('queue');
            $connect = $this->argument('connect') ?? $config['default'];
            $config = $config['connections'][$connect];
            $bindRelations = config('queue_bind');
            $this->args['exchange'] = $bindRelations[$this->args['queue']]['exchange'] ?? false;
            $this->args['exchange_type'] = $bindRelations[$this->args['queue']]['exchange_type'] ?? 'direct';
            $this->args['routing_key'] = $bindRelations[$this->args['queue']]['routing_key'] ?? false;
            $this->channel = $this->rabbitMQConnector->open($this->args, $config);
        } catch (DeclareException $e) {
            $e->report();
            throw new DeclareException();
        }
    }

    public function validateOptions()
    {
        $this->args = $this->option();
    }


}