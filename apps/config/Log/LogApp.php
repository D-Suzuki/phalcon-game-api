<?php

namespace Config\Log;

class LogApp
{
    public static function load()
    {
        return new \Phalbase\Config\Log([
            'log_id'    => 'app_log',
            'file_path' => __DIR__ . '/tmp/app.log',
            'adapter'   => 'File',
            'log_level' => \Phalcon\Logger::ERROR,
        ]);
    }
}
