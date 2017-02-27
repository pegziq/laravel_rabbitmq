<?php
namespace Pegziq\LaravelRabbitMQ\Tests;

use PHPUnit\Framework\TestCase;
use Pegziq\LaravelRabbitMQ\Publisher;
use Pegziq\LaravelRabbitMQ\Consumer;

class LaravelRabbitMQTest extends TestCase
{
    public $publish;
    protected $input;
    public $consumer;
    protected $config;
    public $queue = 'loan';

    public function __construct()
    {
        $this->config = include('Config.php');
        $this->consumer = new Consumer($this->config);
        $this->publish = new Publisher($this->config);
    }

    public function testConnect()
    {
        $this->output('connect success');
        $this->assertTrue($this->consumer->connect->isConnected());
    }

    /**
     * @depends testConnect
     */
//    public function testPublish(){
//        $a = $this->publish->publish(null,'tender','send');
//    }

    /**
     * @depends testConnect
     */
    public function testConsumer(){
        $callback = $this->consumer->callback;
        $this->consumer->consume('loan',function ($message) use ($callback,$app) {
            try {
                App::call([$app->make($callback, [$message]), 'fire']);
            } catch (Exception $e) {
                throw $e;
            }
        });
    }

    public function output($msg){
        echo $msg.PHP_EOL;
    }


    public function tearDown()
    {
    }
}