<?php
namespace Phalbase\Db;

/**
 * \Db\Managerへの状態伝達クラス
 *
 * \Takajo\Bootstrap\ServiceクラスにてDB用イベントリスナーとして登録しており
 * \Phalcon\Db\Adapter\Pdo\[dbms]を使用してのDB操作であれば下記タイミングでPhalconがメソッドをコールする
 * ・afterConnect        DB接続時
 * ・beginTransaction    トランザクションスタート時
 * ・beforeQuery         クエリ実行前
 * ・afterQuery          クエリ実行後
 * ・rollbackTransaction ロールバック時
 * ・commitTransaction   コミット時
 * ・beforeDisconnect    DB切断時
 * ※afterConnectとbeforeDisconnectのみ\Phalbase\Adapter\Pdo\Mysqlで明示的にコール
 * @author suzuki
 */
Class Listener
{

    /**
     * 接続ID
     */
    private $connection_id = null;

    /**
     * DBコネクション毎にListenerインスタンスが生成される
     * コネクションとリスナーオブジェクトは 1:1 の関係
     * @param string $connection_id
     */
    public function __construct(string $connection_id) {
        $this->connection_id = $connection_id;
    }

    /**
     * 接続時
     * コネクションを\Db\ManagerのconnectionPoolへ放り込む
     * @param string $event
     * @param \Phalcon\Db\Adapter $Connection
     */
    public function afterConnect($event, \Phalcon\Db\Adapter $Connection)
    {
        Manager::setConnection($this->connection_id, $Connection);
    }

    /**
     * トランザクションスタート時
     * \Db\Managerへトランザクションスタートしたことを知らせる
     * @param string $event
     * @param \Phalcon\Db\Adapter $Connection
     */
    public function beginTransaction($event, \Phalcon\Db\Adapter $Connection)
    {
        Manager::addBeginedConnectionId($this->connection_id);
    }

    /**
     * クエリ実行前
     *
     * @param string $event
     * @param \Phalcon\Db\Adapter $Connection
     */
    public function beforeQuery($event, \Phalcon\Db\Adapter $Connection)
    {
    }

    /**
     * クエリ実行後
     * クエリのロギングを行う
     * @param string $event
     * @param \Phalcon\Db\Adapter $Connection
     */
    public function afterQuery($event, \Phalcon\Db\Adapter $Connection)
    {
    }

    /**
     * ロールバック時
     * \Db\Managerへロールバックしたことを知らせる
     * @param string $event
     * @param \Phalcon\Db\Adapter $Connection
     */
    public function rollbackTransaction($event, \Phalcon\Db\Adapter $Connection)
    {
        Manager::deleteBeginedTransaction($this->connection_id);
    }

    /**
     * コミット時
     * \Db\Managerへコミットしたことを知らせる
     * @param string $event
     * @param \Phalcon\Db\Adapter $Connection
     */
    public function commitTransaction($event, \Phalcon\Db\Adapter $Connection)
    {
        Manager::deleteBeginedTransaction($this->connection_id);
    }

    /**
     * 切断時
     * @param string $event
     * @param \Phalcon\Db\Adapter $Connection
     */
    public function beforeDisconnect($event, \Phalcon\Db\Adapter $Connection)
    {
        Manager::deleteBeginedTransaction($Connection->getConnectionId());
    }

}
