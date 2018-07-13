<?php

namespace Config;

Class Log extends \Phalcon\Config
{

    /**
     * 設定リスト
     * @var array
     */
    public static $settings = [
        'app_log' => [ // log_id
            'file_path'   => '/tmp/app.log',
            'adapter'     => 'File',
            'log_level'   => \Phalcon\Logger::DEBUG,
        ],
        'player_db_log' => [ // log_id
            'file_path'   => '/tmp/db.log',
            'adapter'     => 'File',
            'log_level'   => \Phalcon\Logger::DEBUG,
        ],
    ];

}