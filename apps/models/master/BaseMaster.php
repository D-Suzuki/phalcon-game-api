<?php

namespace Master;

use Logger\AppLogger;

abstract Class BaseMaster
{

    private function __construct() {}

    /**
     * ファイル名取得
     * @return string
     */
    abstract protected static function getFileName();

    /**
     * マスタデータリスト
     * @var array
     */
    private static $master_list = [];

    /**
     * マスタ全取得
     * @return array
     */
    public static function getAll()
    {
        if (isset(self::$master_list[static::getFileName()]) === FALSE){
			self::loadFile();
		}
        return self::$master_list[static::getFileName()];
    }

    /**
     * 検索
     * @return array
     */
    public static function searchBy()
    {

    }

    /**
     * マスタをロード
     * @return type
     */
    private static function loadFile()
    {
        $file_name   = static::getFileName();
        $master_list = require __DIR__ . '/files/' . $file_name;
        self::$master_list[$file_name] = $master_list;
    }

}
