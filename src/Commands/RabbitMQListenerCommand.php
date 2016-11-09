<?php
namespace Pegziq\LaravelRabbitMQ\Commands;

use Illuminate\Console\Command;
use Pegziq\LaravelRabbitMQ\Connectors\RabbitMQConnector;
use Pegziq\LaravelRabbitMQ\RabbitMQListener;

class RabbitMQListenerCommand extends Command
{
    protected $signature = 'queue_rb:listen {connect?} {--queue=} {--sleep=} {--tries=} {--env=}';
    protected $description = 'rabbitMq监听';
    /**
     * @var RabbitMQListener
     */
    private $listener;
    /**
     * @var RabbitMQConnector
     */
    private $rabbitMQConnector;

    public function __construct(RabbitMQListener $rabbitMQListener,RabbitMQConnector $rabbitMQConnector)
    {
        parent::__construct();
        $this->listener = $rabbitMQListener;
        $this->rabbitMQConnector = $rabbitMQConnector;
    }

    public function fire()
    {
        $this->init();
    }

    public function init(){
        $queue = config('queue');
        $connect = $this->argument('connect') ?: $queue['default'];
        if(!$connect){
            $this->ask('queue连接缺失');
        }
        $this->rabbitMQConnector->connect($queue['connections'][$connect]);
    }

}