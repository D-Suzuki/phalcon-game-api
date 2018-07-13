<?php

Class AppLogger extends \Takajo\Logger\BaseLogger
{

    public static $log_id = 'app_log';

    private static $indent_count = 0;

    private static $process_start_micro_time = null;

    private static $query_count = 0;

    /**
     * プロセス終了
     */
    public static function startProcess()
    {
        parent::log(str_repeat('-', 50));
        parent::log('◆ START Process ◆');
        parent::log(' Start Time:' . str_pad(date('Y-m-d H:i:s'), 37, ' ', STR_PAD_LEFT));
        parent::log(str_repeat('-', 50));
        self::$process_start_micro_time = microtime();
    }

    /**
     * プロセス終了
     */
    public static function endProcess()
    {
        $process_end_micro_time = microtime();
        //$process_micro_sec = $process_end_micro_time - self::$process_start_micro_time;
        parent::log(str_repeat('-', 50));
        parent::log('◆ END Process ◆');
        parent::log(' End Time:' . str_pad(date('Y-m-d H:i:s'), 39, ' ', STR_PAD_LEFT));
        //parent::log(' Process msec:' . str_pad($process_micro_sec * 1000, 35, ' ', STR_PAD_LEFT));
        parent::log(str_repeat('-', 50));
    }

    /**
     * 関数開始
     */
    public static function startFunc(string $method, array $parameters = [])
    {
        parent::log(str_repeat(' ', self::$indent_count) . '▼ ' . $method);
        self::$indent_count++;
        if (count($parameters) > 0) {
            self::outputParameters($parameters);
        }
    }

    /**
     * 関数終了
     */
    public static function endFunc(string $method, $return_value = null)
    {
        self::$indent_count--;
        $return_output = '';
        if (is_null($return_value) === false) {
            $output_value  = self::convertOutputValue($return_value);
            //$return_output = str_repeat(' ', self::$indent_count) . ' - [ ☆ return : ' . $output_value . ' ]';
            parent::log(str_repeat(' ', self::$indent_count) . ' ○ [ return : ' . $output_value . ' ]');
        }
        parent::log(str_repeat(' ', self::$indent_count) . '▲ ' . $method . $return_output);
    }

    /**
     * 関数開始
     */
    public static function execQuery(string $method)
    {
        self::$query_count++;
        parent::log(str_repeat(' ', self::$indent_count) . '#' . self::$query_count . ' QUERY START [' . $method . ']');
    }

    /**
     * パラメータ出力
     */
    private static function outputParameters(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $output_value = self::convertOutputValue($value);
            parent::log(str_repeat(' ', self::$indent_count) . '○ [ ' . $key . ' : ' . $output_value . ' ]');
        }
    }

    /**
     * 出力値変換
     * @param mixed $value
     * @return string
     */
    private static function convertOutputValue($value)
    {
        $convert_value = '';
        if (is_array($value) === true) {
            $convert_value = json_encode($value);
        } elseif (is_bool($value)) {
            $convert_value = $value ? 'true' : 'false';
        } elseif ($value instanceof \DateTime) {
            $convert_value = $value->format('Y-m-d H:i:s');
        } else {
            $convert_value = $value;
        }
        return $convert_value;
    }
    
}