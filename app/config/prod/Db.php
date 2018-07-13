<?php

namespace \Config\Prod;

Class Db extends \Phalcon\Config
{

    /**
     * 設定リスト
     * @var array
     */
    public static $settings = [
        'master_player_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => 'master',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'master_share_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => 'master',
            'charset'     => 'utf8',
            'log_id'      => 'share_db_log'
        ],
        'master_history_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => 'master',
            'charset'     => 'utf8',
            'log_id'      => 'history_db_log'
        ],
        'slave_player_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => 'master',
            'charset'     => 'utf8',
            'log_id'      => 'player_db_log'
        ],
        'slave_share_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => 'master',
            'charset'     => 'utf8',
            'log_id'      => 'share_db_log'
        ],
        'slave_history_db' => [ // connection_id
            'adapter'     => 'Mysql',
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => '',
            'dbname'      => 'master',
            'charset'     => 'utf8',
            'log_id'      => 'history_db_log'
        ],
    ];

}