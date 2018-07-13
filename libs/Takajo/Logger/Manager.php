<?php

namespace Takajo\Logger;

use \Takajo\Config\Manager as ConfigManager;

abstract Class Manager
{

    /**
     * ログインスタンスリスト
     * @var array
     */
    private static $logger_instance_list = [];

    /**
     * ログインスタンス取得
     * @return \Phalcon\Logger\Adapter
     */
    public static function getInstance($log_id)
    {
		if (isset(self::$logger_instance_list[$log_id]) === false) {
            self::$logger_instance_list[$log_id] = self::createInstance($log_id);
        }
        return self::$logger_instance_list[$log_id];
    }

    /**
     * ログインスタンス生成
     * @return \Phalcon\Logger\Adapter
     */
    public static function createInstance($log_id)
    {
        $Config       = ConfigManager::getConfig('log', $log_id);
        $logger_class = '\\Phalcon\\Logger\\Adapter\\' . $Config->adapter;
        $Logger       = new $logger_class($Config->file_path);
        $Logger->setLogLevel($Config->log_level);
        return $Logger;
    }

}