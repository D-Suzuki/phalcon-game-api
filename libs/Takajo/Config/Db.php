<?php

namespace Takajo\Config;

Class Db extends \Phalcon\Config
{

    /**
     * 設定リスト
     * @var array
     */
    public static $settings = [
        'db1' => [
            'adapter'  => 'Mysql',
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'master',
            'charset'  => 'utf8',
            'log_id'   => 'db1_log',
        ],
        'db2' => [
            'adapter'  => 'Mysql',
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'master',
            'charset'  => 'utf8',
            'log_id'   => 'db2_log',
        ],
    ];

}