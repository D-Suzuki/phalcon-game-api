<?php

namespace Phalbase\Logger;

use Phalbase\Logger\Manager as LogManager;

abstract class BaseLogger
{

    /**
     * コネクションID
     * @var int
     */
    public static $log_id = null;

    /**
     * ログ出力
     * @param string $output
     */
    public static function log($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->log($output);
    }

    /**
     * デバッグ出力
     * @param string $output
     */
    public static function debug($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->debug($output);
    }

    /**
     * インフォメーション出力
     * @param string $output
     */
    public static function info($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->info($output);
    }

    /**
     * 通知出力
     * @param string $output
     */
    public static function notice($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->notice($output);
    }

    /**
     * 警告出力
     * @param string $output
     */
    public static function warning($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->warning($output);
    }

    /**
     * エラー出力
     * @param string $output
     */
    public static function error($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->error($output);
    }

    /**
     * アラート出力
     * @param string $output
     */
    public static function alert($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->alert($output);
    }

    /**
     * クリティカル情報出力
     * @param string $output
     */
    public static function critical($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->critical($output);
    }

    /**
     * 緊急情報出力
     * @param string $output
     */
    public static function emergency($output)
    {
        $Logger = LogManager::getInstance(static::$log_id);
        $Logger->emergency($output);
    }

}
