<?php
error_reporting(E_ALL);

use \Phalcon\Loader;
use \Phalcon\Mvc\Application;
use \Takajo\Config\Manager    as ConfigManger;
use \Takajo\Db\Manager        as DbManager;
use \Takajo\Bootstrap\Service as Service;

try {

    /**
     * App定数をロード
     */
    require_once __DIR__ . '/../app/AppConst.php';

    /**
     * Appレジストリを初期化
     */
    require_once __DIR__ . '/../app/AppRegistry.php';
    AppRegistry::intialize();

    /**
     * オートローダー
     */
    $Loader = new Loader();
    $Loader->registerNamespaces([
        'Takajo'       => __DIR__ . '/../libs/Takajo/',
        'Util'         => __DIR__ . '/../libs/Util',
        'Config'       => __DIR__ . '/../app/config/' . AppRegistry::getEnv() . '/',
        //'Logics'   => __DIR__ . '/../app/logics/',
        'Beans'        => __DIR__ . '/../app/models/beans/',
        'Cache'        => __DIR__ . '/../app/models/cache/',
        'Db'           => __DIR__ . '/../app/models/db',
        'GameObject'   => __DIR__ . '/../app/models/game_object/',
        'Master'       => __DIR__ . '/../app/models/master/',
        'PlayerObject' => __DIR__ . '/../app/models/player_object/',
        'Traits'       => __DIR__ . '/../app/models/traits',
        'Logger'       => __DIR__ . '/../app/system/logger/',
    ])->register();
    $Loader->registerDirs([
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/logics/',
        __DIR__ . '/../app/system/logger/',
    ])->register();

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
    $Di      = $Service->createDefaultDi($Config);

    /**
     * リクエスト処理
     */
    $Application = new Application($Di);
    $responce    = $Application->handle()->getContent();

    /**
     * トランザクションコミット
     */
    if (DbManager::hasBeginedConnection()) {
        DbManager::allCommit();
    }

    echo $responce;

} catch (\Exception $e) {

    AppLogger::error($e->getMessage());

    /**
     * トランザクションロールバック
     */
    /*if (DbManager::hasBeginedConnection()) {
        DbManager::allRollback();
    }*/
    echo $e->getMessage();

}