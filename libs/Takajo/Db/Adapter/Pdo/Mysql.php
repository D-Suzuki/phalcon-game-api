<?php

namespace Takajo\Db\Adapter\Pdo;

/**
 * コネクションクラス
 * Phalcon DocumentにはafterConnect/beforeDisconnectイベントが用意されていると書かれているが
 * 実際には実装されていなそうなので\Phalcon\Db\Adapter\Pdo\Mysqlクラスを継承して実現
 * @author suzuki
 */
class Mysql extends \Phalcon\Db\Adapter\Pdo\Mysql
{

    /**
     * 接続
     * @param unknown $descriptor
     * イベント発火のためconnectメソッドをインターセプト
     */
    public function connect(array $descriptor = null)
    {
        if ($descriptor == null) {
            $descriptor = $this->getDescriptor();
        }
        parent::connect($descriptor);

        // afterConnectイベント発火
        if ($this->getEventsManager()) {
            $this->getEventsManager()->fire("db:afterConnect", $this);
        }
    }

    /**
     * 切断
     * イベント発火のためcloseメソッドをインターセプト
     */
    public function close()
    {
        // beforeDisconnectイベント発火
        if ($this->getEventsManager()) {
            $this->getEventsManager()->fire("db:beforeDisconnect", $this);
        }
        parent::close();
    }

}