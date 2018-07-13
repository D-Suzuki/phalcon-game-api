<?php

namespace GameObject\Master;

use Beans\Master\AchiveBean;

Class AchiveMaster extends BaseMaster
{

    /**
     * ファイル名取得
     * @return string
     */
    protected static function getFileName()
    {
        return 'achive_master.php';
    }

    /**
     * アチーブBeanリスト
	 * @var mixed
     */
    private static $achive_bean_list = null;

    /**
     * アチーブID有効判定
     * @param int $gacha_id
     * @return bool
     */
    public static function isValid(int $achive_id)
    {
        \AppLogger::startFunct(__METHOD__, ['$achive_id' => $achive_id]);
        $AchiveBean = self::getAchiveBean($achive_id);
        if (is_null($AchiveBean) === true) {
            $is_valid = false;
        } else {
            $is_valid = true;
        }
        \AppLogger::endFunc(__METHOD__, $is_valid);
        return $is_valid;
    }

    /**
     * アチーブBeanリスト取得
	 * @return array
     */
    public static function getAchiveBeanList()
    {
        \AppLogger::startFunct(__METHOD__);
        if (is_null(self::$achive_bean_list) === true) {
            self::setAchiveBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return self::$achive_bean_list;
    }

    /**
     * 指定アチーブBean取得
     * @param int $achive_id
	 * @return \Beans\AchiveBean
     */
    public static function getAchiveBean(int $achive_id)
    {
        \AppLogger::startFunct(__METHOD__, ['$achive_id' => $achive_id]);
        $AchiveBean       = null;
        $achive_bean_list = self::getAchiveBeanList();
        if (is_null($achive_bean_list) === false && array_key_exists($achive_id, $achive_bean_list) === true) {
            $AchiveBean = $achive_bean_list[$achive_id];
        }
        \AppLogger::endFunc(__METHOD__);
        return $AchiveBean;
    }

    /**
     * アチーブBeanリストセット
     */
    private static function setAchiveBeanList()
    {
        \AppLogger::startFunct(__METHOD__);
        foreach (self::getAll() as $master_data) {
            self::$achive_bean_list[$master_data['achive_id']] = new AchiveBean($master_data);
        }
        \AppLogger::endFunc(__METHOD__);
    }

}