<?php

namespace Pegziq\LaravelRabbitMQ;

use Illuminate\Support\ServiceProvider;
use Pegziq\LaravelRabbitMQ\Connectors\RabbitMQConnector;

class RabbitMQServiceProvider extends ServiceProvider
{
    protected $listener;

    protected $connector;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->registerListenCommand();
        $this->commands(
            'queue_rb::listen'
        );
    }

    protected function registerListenCommand()
    {
        $this->loadClasses();

        $this->app['queue_rb::listen'] = $this->app->share(function () {
            return New Commands\RabbitMQListenerCommand($this->listener,$this->connector);
        });
    }

    protected function loadClasses(){
        $this->listener = new RabbitMQListener();
        $this->connector = new RabbitMQConnector();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('LaravelRabbitMQ', 'Pegziq\LaravelRabbitMQ');
    }
}
