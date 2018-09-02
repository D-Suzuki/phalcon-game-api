<?php

namespace Phalbase\Logger;

abstract Class Manager
{

    private static $config_instance_list = [];

    public static function addConfig(\Phalcon\Config $Config)
    {
        self::$config_instance_list[$Config->log_id] = $Config;
    }

    private static function getConfig($log_id)
    {
        return self::$config_instance_list[$log_id];
    }

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
        $Config       = self::getConfig($log_id);
        $logger_class = '\\Phalcon\\Logger\\Adapter\\' . $Config->adapter;
        $Logger       = new $logger_class($Config->file_path);
        $Logger->setLogLevel($Config->log_level);
        return $Logger;
    }

}
