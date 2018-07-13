<?php

namespace GameObject\Master;

use Beans\Master\GachaNormalMenuBean;

Class GachaNormalMenuMaster extends BaseMaster
{

    /**
     * ファイル名取得
     * @return string
     */
    protected static function getFileName()
    {
        return 'gacha_normal_menu_master.php';
    }

    /**
     * ガチャメニューBeanリスト
	 * @return array[gacha_id][draw_count] = GachaMenuBean
     */
    private static $gacha_menu_bean_list = null;

    /**
     * ガチャメニューBeanリスト取得
	 * @return array
     */
    public static function getGachaMenuBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null(self::$gacha_menu_bean_list) === true) {
            self::setGachaMenuBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return self::$gacha_menu_bean_list;
    }

    /**
     * 指定ガチャメニューBeanリスト取得
     * @param int $gacha_id
     * @return array
     */
    public static function getGachaMenuBeanListByGachaId(int $gacha_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id]);
        $gacha_menu_bean_list = [];
        $gacha_menu_bean_list_each_gacha_id = self::getGachaMenuBeanList();
        if (is_null($gacha_menu_bean_list_each_gacha_id) === false
         && array_key_exists($gacha_id, $gacha_menu_bean_list_each_gacha_id) === true) {
            $gacha_menu_bean_list = $gacha_menu_bean_list_each_gacha_id[$gacha_id];
        }
        \AppLogger::endFunc(__METHOD__);
        return $gacha_menu_bean_list;
    }

    /**
     * 指定ガチャメニューBean取得
     * @param int $gacha_id
     * @param int $draw_count
	 * @return \Beans\MasterData\GachaMenuBean
     */
    public static function getGachaMenuBean(int $gacha_id, int $draw_count)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id, '$draw_count' => $draw_count]);
        $GachaMenuBean        = null;
        $gacha_menu_bean_list = self::getGachaMenuBeanList();
        if (is_null($gacha_menu_bean_list) === false
         && array_key_exists($gacha_id, $gacha_menu_bean_list) === true
         && array_key_exists($draw_count, $gacha_menu_bean_list[$gacha_id]) === true) {
            $GachaMenuBean = $gacha_menu_bean_list[$gacha_id][$draw_count];
        }
        \AppLogger::endFunc(__METHOD__);
        return $GachaMenuBean;
    }

    /**
     * ガチャメニューBeanリストセット
     * @return array
     */
    private static function setGachaMenuBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        foreach (parent::getAll() as $gacha_id => $master_list) {
            foreach ($master_list as $draw_count => $master_data) {
                self::$gacha_menu_bean_list[$gacha_id][$draw_count] = new GachaNormalMenuBean($master_data);
            }
        }
        \AppLogger::endFunc(__METHOD__);
    }

}