<?php

namespace Config;

class Log extends \Phalbase\Config\BaseConfig
{
    public static $settings = [
        [
            'log_id'    => 'app_log',
            'file_path' => '/tmp/app.log',
            'adapter'   => 'File',
            'log_level' => \Phalcon\Logger::ERROR,
        ],
    ];

    public static function get() : \Phalcon\Config
    {
        $Configs = parent::get();
        foreach ($Configs as $Config) {
            $Config->log_level = getenv('LOG_LEVEL');
        }
        return $Configs;
    }
}
