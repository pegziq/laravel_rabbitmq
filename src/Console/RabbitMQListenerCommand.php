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

    public function __construct()
    {
        parent::__construct();
        $config = $this->validateOptions();
        if(!$config){
            throw new \Exception('no config file', 500);
        }
        $this->consumer = new Consumer($config);
    }

    public function fire()
    {
        $this->args = $this->option();
        $closure = $this->callback($this->args['queue']);
        if ($closure) {
            $this->consumer->consume($this->args['queue'], function ($message) use ($closure) {
                try {
                    app()->call([app($closure, [$message]), 'fire']);
                } catch (Exception $e) {
                    throw $e;
                }
            });
        } else {
            throw new \Exception('no callback function', 500);
        }
    }

    public function validateOptions()
    {
        $config = config('queue');
        $machine = $this->argument('connect') ? $this->argument('connect') : 'rabbitmq';
        return isset($config['connections'][$machine]) ? $config['connections'][$machine] : false;
    }

    public function callback($queue)
    {
        $params = $this->consumer->getConfig();
        return isset($params['queue_bind'][$queue]['callback']) ? $params['queue_bind'][$queue]['callback'] : false;
    }


}