<?php

namespace Config;

/**
 * DB設定クラス
 */
class Db extends \Phalbase\Config\BaseConfig
{
    /**
     * 設定値
     * @var array
     */
    static public $settings = [
        [
            'connection_id' => 'read_player_db1',
            'adapter'       => 'Mysql',
            'host'          => 'db',
            'username'      => 'root',
            'password'      => 'rootpass',
            'dbname'        => '',
            'charset'       => 'utf8',
            'port'          => '3306',
        ],
        [
            'connection_id' => 'read_db',
            'adapter'       => 'Mysql',
            'host'          => 'localhost',
            'username'      => 'root',
            'password'      => '',
            'dbname'        => '',
            'charset'       => 'utf8',
            'port'          => '3306',
        ],
    ];

    /**
     * 設定インスタンス取得
     * @return \Phalcon\Config
     */
    public static function get() : \Phalcon\Config
    {
        $Configs = parent::get();
        foreach ($Configs as $Config) {
            switch ($Config->connection_id) {
                case 'write_db':
                    $Config->host = getenv('WRITE_DB_HOST');
                    break;
                case 'read_db':
                    $Config->host = getenv('READ_DB_HOST');
                    break;
                default:
                    break;
            }
        }

        return $Configs;
    }
}
