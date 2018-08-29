<?php

namespace Modules;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * Register a specific autoloader for the module
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $Loader = new Loader();
        $Loader->registerNamespaces(
            [
                'Controllers\Admin;' => APP_PATH . '/controllers/admin/',
                'Takajo'                 => BASE_PATH . '/libs/Takajo/',
                'Util'                   => BASE_PATH . '/libs/Util',
                'Config'                 => CLASSES_PATH . '/config/' . \AppRegistry::getEnv() . '/',
                'Logics'                 => CLASSES_PATH . '/logics/',
                'Beans'                  => CLASSES_PATH . '/models/beans/',
                'Cache'                  => CLASSES_PATH . '/models/cache/',
                'Db'                     => CLASSES_PATH . '/models/db',
                'GameObject'             => CLASSES_PATH . '/models/game_object/',
                'PlayerObject'           => CLASSES_PATH . '/models/player_object/',
                'Traits'                 => CLASSES_PATH . '/models/traits',
                'Logger'                 => CLASSES_PATH . '/system/logger/',
            ]
        );
        $Loader->register();
    }

    /**
     * Register specific services for the module
     */
    public function registerServices(DiInterface $Di)
    {
        // Registering a dispatcher
        $Di->set(
            "dispatcher",
            function () {
                $Dispatcher = new Dispatcher();
                $Dispatcher->setDefaultNamespace('Apps\Admin\Controllers');
                return $Dispatcher;
            }
        );

        // Registering the view component
        $Di->set(
            "view",
            function () {
                $view = new View();
                // $view->setViewsDir("../apps/backend/views/");
                return $view;
            }
        );
    }
}
