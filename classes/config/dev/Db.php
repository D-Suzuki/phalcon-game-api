<?php

namespace Config;

Class Db extends \Phalcon\Config
{

    /**
     * 設定リスト
     * @var array
     */
    public static $settings = [
        'read_player_db1' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'port'        => 3306,
            'username'    => 'root',
            'password'    => '',
            'dbname'      => '',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'read_share_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'port'        => 3306,
            'username'    => 'root',
            'password'    => '',
            'dbname'      => '',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'read_player_db2' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'port'        => 3306,
            'username'    => 'root',
            'password'    => '',
            'dbname'      => '',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'write_player_db1' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'port'        => 3306,
            'username'    => 'root',
            'password'    => '',
            'dbname'      => '',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'write_share_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => '',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'slave_history_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => '',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'write_history_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => '',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
    ];

}