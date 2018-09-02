<?php
error_reporting(E_ALL);

use \Phalcon\Di\FactoryDefault;
use \Phalcon\Loader;
use \Phalcon\Mvc\Router;
use \Phalcon\Mvc\Dispatcher;

define('BASE_PATH',   dirname(__DIR__));
define('APPS_PATH',   BASE_PATH . '/apps');
define('CONFIG_PATH', BASE_PATH . '/config');
define('LIBS_PATH',   BASE_PATH . '/libs');
define('SYSTEM_PATH', BASE_PATH . '/system');
define('TASKS_PATH',  BASE_PATH . '/tasks');

try {

    /**
     * オートローダー登録
     */
    $Loader = new Loader();
    $Loader->registerDirs(
        [
            APPS_PATH,
            APPS_PATH . '/logger/',
        ]
    );
    $Loader->registerNamespaces(
        [
            'Phalbase'     => LIBS_PATH . '/Phalbase',
            'Util'         => LIBS_PATH . '/Util',
            'Config'       => APPS_PATH . '/config',
            'Controllers'  => APPS_PATH . '/controllers',
            'Logger'       => APPS_PATH . '/logger',
            'Logics'       => APPS_PATH . '/logics',
            'Beans'        => APPS_PATH . '/models/beans',
            'Cache'        => APPS_PATH . '/models/cache',
            'Db'           => APPS_PATH . '/models/db',
            'GameObject'   => APPS_PATH . '/models/game_object',
            'Master'       => APPS_PATH . '/models/master',
            'PlayerObject' => APPS_PATH . '/models/player_object',
            'Traits'       => APPS_PATH . '/models/traits',
        ]
    );
    $Loader->register();

    /**
     * Appレジストリを初期化
     */
    AppRegistry::intialize();

    /**
     * ルーティング設定
     */
    $Di = new FactoryDefault();
    $Di->set('router', function() {
        return require_once APPS_PATH . '/routes.php';
    });
    $Router = $Di->get('router');
    $Router->handle($_SERVER['REQUEST_URI']);

    /**
     * ディスパッチャー
     */
    $Di->set(
        'dispatcher',
        function () use($Router) {
            $Dispatcher = new Dispatcher();
            $Dispatcher->setDefaultNamespace('Controllers\\' . $Router->getModuleName());
            return $Dispatcher;
        }
    );

    /*$Di->set(
        "view",
        function () {
            $view = new View();
            // $view->setViewsDir("../apps/backend/views/");
            return $view;
        }
    );*/

    /**
     * 設定クラスセット
     */
    foreach (Config\Db::get() as $Config) {
        Phalbase\Db\Manager::addConfig($Config);
    }
    foreach (Config\Log::get() as $Config) {;
        Phalbase\Logger\Manager::addConfig($Config);
    }

    /**
     * DIコンテナセット
     */
	//$Config  = ConfigManager::getConfig('di');
    //$Service = new Service();
    //$Di      = $Service->createDefaultDi($Config);

    /**
     * ディスパッチ
     */
    $Dispatcher = $Di->getShared('dispatcher');
    $Dispatcher->setModuleName($Router->getModuleName());
    $Dispatcher->setControllerName($Router->getControllerName());
    $Dispatcher->setActionName($Router->getActionName());
    $Dispatcher->setParams($Router->getParams());
    $Dispatcher->dispatch();

    if (Phalbase\Db\Manager::hasBeginedConnection() === true) {
        Phalbase\Db\Manager::allCommit();
    }

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
