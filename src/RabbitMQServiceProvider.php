<?php

namespace Pegziq\LaravelRabbitMQ;

use Illuminate\Support\ServiceProvider;

class RabbitMQServiceProvider extends ServiceProvider
{
    protected $consumer;

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

        $this->app['queue_rb::listen'] = $this->app->share(function ($app) {
            return New Console\RabbitMQListenerCommand($this->consumer,$app);
        });
    }

    protected function loadClasses(){
        $this->consumer = new Consumer();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('LRMQ', 'Pegziq\LaravelRabbitMQ');
    }
}
