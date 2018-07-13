<?php

namespace GameObject\Master;

use Beans\Master\EventBean;

Class EventMaster extends BaseMaster
{

    /**
     * ファイル名取得
     * @return string
     */
    protected static function getFileName()
    {
        return 'event_master.php';
    }

    const EVENT_TYPE_A = 1;
    const EVENT_TYPE_B = 2;

    /**
     * イベントBeanリスト
	 * @var mixed
     */
    private static $event_bean_list = null;

    /**
     * イベントID有効判定
     * @param int $event_id
     * @return bool
     */
    public static function isValid(int $event_id)
    {
        \AppLogger::startFunct(__METHOD__, ['$event_id' => $event_id]);
        $EventBean = self::getEventBean($event_id);
        if (is_null($EventBean) === true) {
            $is_valid = false;
        } else {
            $is_valid = true;
        }
        \AppLogger::endFunc(__METHOD__, $is_valid);
        return $is_valid;
    }

    /**
     * イベントBeanリスト取得
	 * @return array
     */
    public static function getEventBeanList()
    {
        \AppLogger::startFunct(__METHOD__);
        if (is_null(self::$event_bean_list) === true) {
            self::setEventBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return self::$event_bean_list;
    }

    /**
     * 指定イベントBean取得
     * @param int $event_id
	 * @return \Beans\MasterData\EventBean
     */
    public static function getEventBean(int $event_id)
    {
        \AppLogger::startFunct(__METHOD__, ['$event_id' => $event_id]);
        $EventBean       = null;
        $event_bean_list = self::getEventBeanList();
        if (is_null($event_bean_list) === false && array_key_exists($event_id, $event_bean_list) === true) {
            $EventBean = $event_bean_list[$event_id];
        }
        \AppLogger::endFunc(__METHOD__);
        return $EventBean;
    }

    /**
     * イベントBeanリストセット
     */
    private static function setEventBeanList()
    {
        \AppLogger::startFunct(__METHOD__);
        foreach (self::getAll() as $master_data) {
            self::$event_bean_list[$master_data['event_id']] = new EventBean($master_data);
        }
        \AppLogger::endFunc(__METHOD__);
    }

}