<?php
namespace Pegziq\LaravelRabbitMQ\Tests;

use PHPUnit\Framework\TestCase;
use Pegziq\LaravelRabbitMQ\Publisher;
use Pegziq\LaravelRabbitMQ\Consumer;

class LaravelRabbitMQTest extends TestCase
{
    public $publish;
    public $consumer;
    protected $config;
    public $exchange_name = 'test';
    public $queue_name = 'test';

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
    public function testPublish()
    {
        /**
         * 为了初始化queue和exchange，如果保证有queue和exchange，可省略
         */
        $this->publish->channel($this->exchange_name, $this->queue_name);
        $this->publish->channel->queue_declare($this->queue_name, false, true, false, false);
        $this->publish->channel->queue_bind($this->queue_name, $this->exchange_name);
        //end
        $ret = $this->publish->publish('one message', $this->exchange_name, '', ['delivery_mode' => 2]);
        $this->output('message send success');
        $this->assertNull($ret);
        return $ret;
    }

    /**
     * @depends testPublish
     */
    public function testConsumer($ret)
    {
        $this->consumer->consume($this->queue_name, function ($message) {
            try {
                $action = new Action($message);
                $this->output('message receive success');
                $ret = $action->fire();
                $this->assertTrue($ret);
            } catch (Exception $e) {
                throw $e;
            }
        });
        return true;
    }

    public function output($msg)
    {
        echo $msg . PHP_EOL;
    }


    public function tearDown()
    {
    }
}