<?php
namespace Phalbase\Db;

/**
 * DBの状態管理クラス
 * @author suzuki
 */
abstract class Manager
{

    /**
     * DB設定インスタンスリスト
     * @var array[] = \Phalcon\Config
     */
    private static $config_instance_list = [];

    /**
     * カスタムリスナー
     * @var \Phalbase\Db\Listener
     */
    private static $CustomListener = null;

    /**
     * コネクションプール
     * @var array[$connection_id] = \Phalcon\Db\Adapter
     */
    private static $connection_pool = [];

    /**
     * トランザクションスタートしたコネクション配列
     * @var array
     */
    private static $begined_connection_id_list = [];

    /**
     * コミットされたコネション配列
     * @var array
     */
    private static $commited_connection_id_list = [];

    /**
     * 設定インスタンス追加
     * @param \Phalcon\Config
     */
    public static function addConfig(\Phalcon\Config $Config)
    {
        self::$config_instance_list[$Config->connection_id] = $Config;
    }

    /**
     * 設定インスタンス取得
     * @return \Phalcon\Config
     */
    private static function getConfig($connection_id)
    {
        return self::$config_instance_list[$connection_id];
    }

    /**
     * カスタムリスナー追加
     * @param \ßPhalbase\Db\Listener $Listener
     */
    public static function setCustomListener(\Phalbase\Db\Listener $Listener)
    {
        self::$CustomListener = $Listener;
    }

    /**
     * コネクション取得
	 * @param string $connection_id
     */
    public static function getConnection($connection_id)
    {
        $Config = self::getConfig($connection_id);
        if (isset(self::$connection_pool[$Config->host]) === false) {
           self::$connection_pool[$Config->host] = self::createConnection($connection_id);
        }
        return self::$connection_pool[$Config->host];
    }

    /**
     * トランザクションスタートした接続ID追加
     * @param string $connection_id
     * @param \Phalcon\Db\Adapter $Connection
     */
    public static function setConnection($connection_id, \Phalcon\Db\Adapter $Connection)
    {
        $Config = self::getConfig($connection_id);
        if (isset(self::$connection_pool[$Config->host]) === false) {
            self::$connection_pool[$Config->host] = $Connection;
        }
    }

    /**
     * トランザクションスタートした接続ID追加
     * @param string $connection_id
     */
    public static function addBeginedConnectionId($connection_id)
    {
        if (in_array($connection_id, self::$begined_connection_id_list) === false) {
            self::$begined_connection_id_list[] = $connection_id;
        }
    }

    /**
     * コミットされた接続ID追加
     * @param string $connection_id
     */
    public static function addCommitedConnectionId($connection_id)
    {
        if (in_array($connection_id, self::$commited_connection_id_list) === false) {
            self::$commited_connection_id_list[] = $connection_id;
        }
    }

    /**
     * トランザクションエンド
     * @param string $connection_id
     */
    public function deleteBeginedTransaction($connection_id) {
        if (($key = array_search($connection_id, self::$begined_connection_id_list)) !== false) {
            unset(self::$begined_connection_id_list[$key]);
        }
    }

    /**
     * トランザクションスタートしているコネクションがあるかの判定
     * @return boolean
     */
    public static function hasBeginedConnection()
    {
        return self::$begined_connection_id_list ? true : false;
    }

    /**
     * 全コミット
     * @return void
     */
    public static function allCommit()
    {
        if (self::hasBeginedConnection() === true) {
            foreach (self::$begined_connection_id_list as $connection_id) {
                $Connection = self::getConnection($connection_id);
                // ネストされている場合も考慮してトランザクションレベルが0になるまで行う
                while ($Connection->getTransactionLevel() != 0) {
                    $is_nesting = $Connection->getTransactionLevel() > 1 ? true : false;
                    $Connection->commit($is_nesting);
                }
            }
        }
    }

    /**
     * コネクション生成
     * @param string $connection_id
	 * @param \Takajo\Config\Db $DbConfig
     */
    private static function createConnection(string $connection_id)
    {
        // 接続情報
        $Config     = self::getConfig($connection_id);
        $descriptor = self::createDescriptor($Config);

        // DB接続
        $connection_class = '\\Phalbase\\Db\\Adapter\\Pdo\\' . $Config->adapter;
        $Connection       = new $connection_class($descriptor);

        // DBリスナー
        if (is_null(self::$CustomListener) === true) {
            $Listener = new \Phalbase\Db\Listener($connection_id);
        } else {
            $Listener = self::$CustomListener;
        }

        // DBコネクションのイベントマネージャを登録
        $EventsManager = new \Phalcon\Events\Manager();
        $EventsManager->attach('db', $Listener);
        $Connection->setEventsManager($EventsManager);

        // 接続
        $Connection->connect();

        return $Connection;
    }

    /**
     * 接続情報生成
     * @param \Phalcon\Config $Config
	 * @return array
     */
    private static function createDescriptor(\Phalcon\Config $Config)
    {
        $descriptor = [
            'host'     => $Config->host,
            'username' => $Config->username,
            'password' => $Config->password,
            'dbname'   => $Config->dbname,
            'charset'  => $Config->charset,
            'port'     => $Config->port,
        ];
        if ($Config->offsetExists('pdo_options') === true &&
            is_array($Config->pdo_options)       === true) {
            $descriptor['options'] = $Config->pdo_options->toArray();
        }

        return $descriptor;
    }

}
