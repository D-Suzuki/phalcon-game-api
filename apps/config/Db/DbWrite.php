<?php

namespace Config\Db;

use \Phalbase\Config\Db as DbConfig;

class DbWrite
{
    public static function load()
    {
        return new DbConfig([
            'connection_id' => 'write_db',
            'adapter'       => 'Mysql',
            'host'          => 'localhost',
            'username'      => 'root',
            'password'      => '',
            'dbname'        => getenv('TEST'),
            'charset'       => 'utf8',
            'log_id'        => 'db1_log',
        ]);
    }
}
