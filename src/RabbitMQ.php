<?php
namespace Pegziq\LaravelRabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;

class RabbitMQ
{
    public $connect;
    protected $channel;
    public $durable=true;
    protected $config;
    public $exchange_name;
    public $queue_name;
    public $routing_key;

    public function __construct($config=array())
    {
        $this->setConfig($config);
        $this->connection();
    }

    public function connection()
    {
        if(!$this->connect){
            try{
                $config = $this->config;
                return $this->connect = new AMQPStreamConnection(
                    $config['host'],
                    $config['port'],
                    $config['login'],
                    $config['password'],
                    $config['vhost'],
                    isset($config['insist']) ? $config['insist'] : false,
                    isset($config['login_method']) ? $config['login_method'] : 'AMQPLAIN',
                    isset($config['login_response']) ? $config['login_response'] : null,
                    isset($config['locale']) ? $config['locale'] : 'en_US',
                    isset($config['connection_timeout']) ? $config['connection_timeout'] : 3,
                    isset($config['read_write_timeout']) ? $config['read_write_timeout'] : 3,
                    isset($config['context']) ? $config['context'] : null,
                    isset($config['keepalive']) ? $config['keepalive'] : false,
                    isset($config['heartbeat']) ? $config['heartbeat'] : 0
                );
            }catch (AMQPProtocolConnectionException  $e){
                throw new \Exception('cannot connect rabbitmq',500);
            }
        }
    }

    protected function channel($exchange_name='',$queue_name='')
    {
        if ($queue_name && !$exchange_name){
            $queue_bind = isset($this->config['queue_bind']) ? $this->config['queue_bind'] : config('queue_bind');
            $exchange_name = $queue_bind['exchange'];
        }
        $exchange_bind = isset($this->config['exchange_bind']) ? $this->config['exchange_bind'] : config('exchange_bind');
        $exchange_type = $exchange_bind[$exchange_name]['exchange_type'];
        $this->channel = $this->connect->channel();
        $this->channel->exchange_declare($exchange_name, $exchange_type, false, true, false);
        return $this->channel;
    }

    public function channel_close(){
        $this->channel->close();
    }

    public function connect_close(){
        $this->connect->close();
    }

    public function acknowledge($message)
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        if ($message->body === 'quit') {
            $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        }
    }

    /**检查配置
     * @param array $config
     */
    public function setConfig(array $config)
    {
        if (!($config['host'] && $config['port'] && $config['login'] && $config['password'])) {
            throw new Exception('config is empty');
        }
        empty($config['vhost']) && $config['vhost'] =  '/';
        $this->config = $config;
    }

    /*
     * 设置是否持久化，默认为True
     */
    public function setDurable($durable) {
        $this->durable = $durable;
    }
}
