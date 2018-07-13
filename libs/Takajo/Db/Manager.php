<?php
namespace Takajo\Db;

use \Takajo\Config\Manager as ConfigManager;
use \Takajo\Logger\Manager as LoggerManager;

/**
 * DBの状態管理クラス
 * @author suzuki
 */
abstract class Manager
{

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
     * コネクション取得
	 * @param string $connection_id
     */
    public static function getConnection($connection_id)
    {
        if (isset(self::$connection_pool[$connection_id]) === false) {
           self::$connection_pool[$connection_id] = self::createConnection($connection_id);
        }
        return self::$connection_pool[$connection_id];
    }
    
    /**
     * トランザクションスタートした接続ID追加
     * @param string $connection_id
     * @param \Phalcon\Db\Adapter $Connection
     */
    public static function setConnection($connection_id, \Phalcon\Db\Adapter $Connection)
    {
        if (isset(self::$connection_pool[$connection_id]) === false) {
            self::$connection_pool[] = $Connection;
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

    public static function allCommit()
    {
        if (self::hasBeginedConnection() === true) {
            foreach (self::$begined_connection_id_list as $connection_id) {
                do {
                    $Connection = self::getConnection($connection_id);
                    $is_nesting = $Connection->getTransactionLevel() > 1 ? true : false;
                    $Connection->commit($is_nesting);
                    // ネストされている場合も考慮してトランザクションレベルが0になるまで行う
                } while ($Connection->getTransactionLevel() != 0);
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
        $Config     = ConfigManager::getConfig('db', $connection_id);
        $descriptor = self::createDescriptor($Config);

        // DB接続
        $connection_class = '\\Takajo\\Db\\Adapter\\Pdo\\' . $Config->adapter;
        $Connection       = new $connection_class($descriptor);

        // DBリスナーオブジェクトを生成
		$Logger   = LoggerManager::getInstance($Config->log_id);
        $Listener = new \Takajo\Db\Listener($connection_id, $Logger);

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