<?php
error_reporting(E_ALL);

use \Phalcon\Mvc\Application;
use \Phalcon\Di\FactoryDefault;
use \Phalcon\Loader;
use \Phalcon\Mvc\Router;

use \Takajo\Config\Manager    as ConfigManger;
use \Takajo\Db\Manager        as DbManager;
use \Takajo\Bootstrap\Service as Service;

define('BASE_PATH',    dirname(__DIR__));
define('APP_PATH',     BASE_PATH . '/apps');
define('CLASSES_PATH', BASE_PATH . '/classes');

try {

    /**
     * App定数をロード
     */
    require_once __DIR__ . '/../classes/AppConst.php';

    /**
     * Appレジストリを初期化
     */
    require_once __DIR__ . '/../classes/AppRegistry.php';
    AppRegistry::intialize();

    /**
     * ルーティング設定
     */
    $Di = new FactoryDefault;
    $Di->set('router', function() {
        return require_once '../apps/routes.php';
    });

    $Router = $Di->get('router');
    $Router->handle($_SERVER['REQUEST_URI']);

    /**
     * モジュールロード
     */
    switch ($Router->getModuleName()) {
        case 'game':
            require_once APP_PATH . '/modules/game/Module.php';
            $Module = new \Modules\Module;
            break;
        case 'admin':
            require_once APP_PATH . '/modules/game/Module.php';
            $Module = new \Modules\Module;
            break;
        default:
            throw new \RuntimeException('unknown module');
    }
    $Module->registerAutoloaders($Di);
    $Module->registerServices($Di);


    /**
     * 設定クラスセット
     */
    ConfigManger::setClass('app',      Config\App::class);
    ConfigManger::setClass('db',       Config\Db::class);
    ConfigManger::setClass('di',       Config\Di::class);
    ConfigManger::setClass('log',      Config\Log::class);
    ConfigManger::setClass('memcache', Config\Memcache::class);
    ConfigManger::setClass('redis',    Config\Redis::class);

    /**
     * DIコンテナセット
     */
	$Config  = ConfigManger::getConfig('di');
    $Service = new Service();
    //$Di      = $Service->createDefaultDi($Config);

    $Dispatcher = $Di->getShared('dispatcher');
    $Dispatcher->setModuleName($Router->getModuleName());
    $Dispatcher->setControllerName($Router->getControllerName());
    $Dispatcher->setActionName($Router->getActionName());
    $Dispatcher->setParams($Router->getParams());

    $Dispatcher->dispatch();


} catch (\Exception $e) {
echo $e->getMessage();exit;
    AppLogger::error($e->getMessage());

    /**
     * トランザクションロールバック
     */
    /*if (DbManager::hasBeginedConnection()) {
        DbManager::allRollback();
    }*/
    echo $e->getMessage();

}
