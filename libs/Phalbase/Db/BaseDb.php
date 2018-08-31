<?php

namespace Phalbase\Db;

use Sysbase\Db\Manager as DbManager;

abstract class Basedb
{

    /* ▼ -------------------- ▼ private propety ▼ -------------------- ▼ */

    /**
     * コネクションID
     * @var int
     */
    private $connection_id = null;

    /**
     * クエリ
     * @var string
     */
    private $query = null;

    /**
     * バインドパラメーター
     * @var array
     */
    private $bindParams = array();

    /**
     * バインドパラメータータイプ
     * @var array
     */
    private $bindTypes = array();

    /* ▲ -------------------- ▲ private propety ▲ -------------------- ▲ */
    /* ▼ -------------------- ▼ public function ▼ -------------------- ▼ */

    /**
     * コンストラクタ
     * @param string $connection_id
     */
    final public function __construct($connection_id)
    {
		$this->connection_id = $connection_id;
    }

    /**
     *queryへのsetter
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * queryへのgetter
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * bindParamsへのsetter
     * @param array $bindParams
     */
    public function setBindParams($bindParams)
    {
        $this->bindParams = $bindParams;
    }

    /**
     * bindParmasへの追加（値単位）
     * @param mixed $bindParam
     */
    public function addBindParam($bindParam)
    {
        array_push($this->bindParams, $bindParam);
    }

    /**
     * bindParmasへの追加（配列単位）
     * @param unknown $bindParams
     */
    public function addBindParams($bindParams)
    {
        $this->bindParams = array_merge($this->bindParams, $bindParams);
    }

    /**
     * bindParamsへのgetter
     * @return array
     */
    public function getBindParams()
    {
        return $this->bindParams;
    }

    /**
     * bindTypesへのsetter
     * @param array $bindTypes
     */
    public function setBindTypes($bindTypes)
    {
    	$this->bindTypes = $bindTypes;
    }

    /**
     * bindTypesへの追加（値単位）
     * @param unknown $bindType
     */
    public function addBindType($bindType)
    {
    	array_push($this->bindTypes, $bindType);
    }

    /**
     * bindTypesへの追加（配列単位）
     * @parzam unknown $bindTypes
     */
    public function addBindTypes($bindTypes)
    {
    	$this->bindTypes = array_merge($this->bindTypes, $bindTypes);
    }

    /**
     * bindTypesへのgetter
     * @return array
     */
    public function getBindTypes()
    {
    	return $this->bindTypes;
    }

    /**
     * クエリとパラメーターをリセット
     */
    public function resetQueryAndParams()
    {
        $this->query      = null;
        $this->bindParams = [];
        $this->bindTypes  = [];
    }

    /**
     * クエリの実行(SELECT)
     * @return array
     */
    public function select()
    {
        $Connection = DbManager::getConnection($this->connection_id);
        if (empty($this->bindTypes) === TRUE) {
            $result = $Connection->query($this->getQuery(), $this->getBindParams());
        } else {
            $result = $Connection->query($this->getQuery(), $this->getBindParams(), $this->getBindTypes());
        }
        $result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
		$this->resetQueryAndParams();

        return $result->fetchAll();
    }

    /**
     * クエリの実行(SELECT) - 1行のみ取得
     * @return array
     */
    public function selectRow()
    {
        $Connection = DbManager::getConnection($this->connection_id);
        if (empty($this->getBindTypes()) === TRUE) {
            $result = $Connection->query($this->getQuery(), $this->getBindParams());
        } else {
            $result = $Connection->query($this->getQuery(), $this->getBindParams(), $this->getBindTypes());
        }
        $result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $this->resetQueryAndParams();

        return $result->fetch();
    }

    /**
     * PDOステートメント取得
     * @return PDOStatement
     */
    public function getPdoStatement()
    {
        $Connection = DbManager::getConnection($this->connection_id);
        $Statement  = $Connection->prepare($this->getQuery());
        if (empty($this->getBindTypes()) === TRUE) {
            $Statement = $Connection->executePrepared($statement, $this->getBindParams());
        } else {
        	$Statement = $Connection->executePrepared($statement, $this->getBindParams(), $this->getBindTypes());
        }

        return $Statement;
    }

    /**
     * クエリの実行(INSERT / UPDATE / DELETE)
     * @return
     */
    public function exec()
    {
        $Connection = DbManager::getConnection($this->connection_id);
        if (empty($this->getBindTypes()) === TRUE) {
            $result = $Connection->execute($this->getQuery(), $this->getBindParams());
            $this->resetQueryAndParams();
            return $result;
        } else {
        	$result = $Connection->execute($this->getQuery(), $this->getBindParams(), $this->getBindTypes());
        	$this->resetQueryAndParams();
        	return $result;
        }
    }

    /**
     * ラストインサートID取得
     * @return
     */
    public function getLastInsertId()
    {
        $Connection = DbManager::getConnection($this->connection_id);
        return $Connection->lastInsertId();
    }

    /**
     * トランザクションスタート
     */
    public function beginTransaction()
    {
        $Connection = DbManager::getConnection($this->connection_id);
        $Connection->begin();
    }

    /**
     * 切断
     */
    public function closeConnection()
    {
        $Connection = DbManager::getConnection($this->connection_id);
        $Connection->close();
    }

    /**
     * 再接続
     * @param boorean $isBegin
     */
    public function reConnect($isBegin = false)
    {
        $Connection = DbManager::getConnection($this->connetin_id);
        $Connection->connect();
        if ($isBegin) {
            $this->beginTransaction();
        }
    }

    /* ▲ -------------------- ▲ public function ▲ -------------------- ▲ */

}
