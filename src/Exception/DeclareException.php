<?php

namespace Pegziq\LaravelRabbitMQ;

use Exception;

class DeclareException extends Exception
{

    public function report(){
        return false;
    }
}
