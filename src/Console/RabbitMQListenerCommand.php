<?php
namespace Pegziq\LaravelRabbitMQ\Console;

use Illuminate\Console\Command;
use Pegziq\LaravelRabbitMQ\Consumer;

class RabbitMQListenerCommand extends Command
{
    public $args;
    protected $signature = 'queue_rb:listen {connect?} {--queue=} {--sleep=} {--tries=} {--env=}';
    protected $description = 'rabbitMq Listener';
    protected $config;
    protected $queue_bind;
    private $consumer;
    public $connection;

    public function __construct(Consumer $consumer)
    {
        parent::__construct();
        $this->consumer = $consumer;
    }

    public function fire()
    {
        $this->args = $this->option();
        $connect = $this->validateOptions();
        $closure = $this->callback();
        if ($closure) {
            $this->consumer->init($connect, $this->args['queue']);
            $this->consumer->consume($this->args['queue'], function ($message) use ($closure) {
                try {
                    app()->call([app($closure, [$message]), 'fire']);
                } catch (Exception $e) {
                    throw $e;
                }
            });
        }
    }

    public function validateOptions()
    {
        $config = config('queue');
        $machine = $this->argument('connect') ?? 'rabbitmq';
        return $config['connections'][$machine];
    }

    public function callback()
    {
        $params = $this->consumer->getParams($this->args['queue']);
        return $params['callback'] ?? false;
    }


}