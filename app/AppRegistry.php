<?php

/**
 * アプリケーションレジストリクラス
 */
abstract Class AppRegistry
{

    /**
     * アクセス日時
     * @var DateTime
     */
	private static $AccessTime = null;

	/**
	 * 実行環境
	 * @var string
	 */
	private static $env = null;

	/**
	 * DBタイプ
	 * @var string    
	 */
	private static $db_type = null;

    /**
     * 初期化
     */
    public static function intialize()
    {
		self::$AccessTime = new DateTime();
		self::$env        = AppConst::ENV_DEV;
		self::$db_type    = AppConst::DB_TYPE_WRITE;
    }

    /**
     * アクセス日時取得
     * @return DateTime
     */
    public static function getAccessTime()
    {
        // 書き換えられたくないのでcloneする
        return clone self::$AccessTime;
	}

    /**
     * 実行環境取得
     * @return string
     */
    public static function getEnv()
    {
        return self::$env;
	}

    /**
     * DBタイプセット
     * @param string $db_type
     */
	public static function setDbType(string $db_type)
	{
	    if ($db_type != AppConst::DB_TYPE_READ && $db_type != AppConst::DB_TYPE_WRITE) {
	        throw new Exception();
	    }
	    self::$db_type = $db_type;
	}

    /**
     * DBタイプ取得
     * @return string
     */
    public static function getDbType()
    {
        return self::$db_type;
    }

}
