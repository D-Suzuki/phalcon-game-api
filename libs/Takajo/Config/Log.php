<?php

namespace Takajo\Config;

Class Log extends \Phalcon\Config
{

    /**
     * 設定リスト
     * @var array
     */
    public static $settings = [
        'app_log' => [ // log_id
            'file_path'   => __DIR__ . '/../../app/log/app.log',
            'adapter'     => 'File',
            'log_level'   => \Phalcon\Logger::ERROR,
        ],
        'db_log' => [ // log_id
            'file_path'   => __DIR__ . '/../../app/log/db.log',
            'adapter'     => 'File',
            'log_level'   => \Phalcon\Logger::ERROR,
        ],
    ];

}