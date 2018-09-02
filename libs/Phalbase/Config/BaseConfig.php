<?php

namespace Phalbase\Config;

/**
 * 設定基底クラス
 */
abstract class BaseConfig
{
    /**
     * 設定インスタンスリスト
     * @var array[\Phalcon\Config]
     */
    private static $instance_list = [];

    /**
     * 設定インスタンス取得
     * @return \Phalcon\Config
     */
    public static function get() : \Phalcon\Config
    {
        if (isset($instance_list[static::class]) === false) {
            self::set();
        }
        return self::$instance_list[static::class];
    }

    /**
     * 設定インスタンスセット
     * @return void
     */
    private static function set()
    {
        self::$instance_list[static::class] = new \Phalcon\Config(static::$settings);
    }
}
