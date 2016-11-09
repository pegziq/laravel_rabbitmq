<?php
namespace Pegziq\LaravelRabbitMQ\Connectors;

use Illuminate\Queue\Connectors\ConnectorInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConnector implements ConnectorInterface
{

    public function connect(array $config)
    {
        $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['login'], $config['password'], $config['vhost']);
        return $connection;
    }

}