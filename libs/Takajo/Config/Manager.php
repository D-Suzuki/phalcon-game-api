<?php

namespace Takajo\Config;

abstract Class Manager
{
    
    /**
     * 設定インスタンスリスト
     * @var array
     */
    private static $config_instance_list = [];

    /**
     * 設定リスト
     * @var array
     */
    private static $config_class_list = [
        'app' => '\\Takajo\Config\App',
        'di'  => '\\Takajo\Config\Di',
        'db'  => '\\Takajo\Config\Db',
        'log' => '\\Takajo\Config\Log',
    ];

    /**
     * 設定リスト
     * @var array
     */
    public static function setClass($key, $class)
    {
        self::$config_class_list[$key] = $class;
	}

    /**
     * 設定取得
     * @return \\Phalcon\Config
     */
    public static function getConfig($class_key, $search_key = null)
    {
        return self::load(self::$config_class_list[$class_key], $search_key);
    }

    /**
     * 設定ロード
     * @return \Phalcon\Config
     */
    private static function load($class, $key = null)
    {
        if (isset(self::$config_instance_list[$class]) === false) {
            self::$config_instance_list[$class] = new $class($class::$settings);
        }
        if (is_null($key) === false) {
            if ( isset(self::$config_instance_list[$class][$key]) === true) {
                return self::$config_instance_list[$class][$key];
            } else {
                throw new \Exception('not exists config key[class=' . $class . ':key=' . $key . ']');
            }
        } else {
            return self::$config_instance_list[$class];
        }
    }

}