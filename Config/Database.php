<?php


namespace Config;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Database
{

    public function __construct()
    {

        $capsule = new Capsule;

        $capsule->addConnection([

            'determineRouteBeforeAppMiddleware' => false,
            'displayErrorDetails' => true,

            // 'driver'    => 'mysql',
            // 'host'      => 'localhost',
            // 'database'  => '******',
            // 'username'  => '******',
            // 'password'  => 'H|*sft:c5L',
            // 'charset'   => 'utf8',
            // 'collation' => 'utf8_unicode_ci',
            // 'prefix'    => '',


        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}

?>