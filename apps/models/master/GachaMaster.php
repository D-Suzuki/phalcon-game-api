<?php

namespace Master;

use Beans\Master\GachaBean;
use Logger\AppLogger;

Class GachaMaster extends BaseMaster
{

    /**
     * @const ガチャタイプ
     */
    const GACHA_TYPE_NORAMAL = 1; // ノーマル
    const GACHA_TYPE_STEP_UP = 2; // ステップアップ
    const GACHA_TYPE_BOX     = 3; // ボックス

    /**
     * 価格タイプ
     */
    const PRICE_TYPE_JEWEL = 1; // ジュエル
    const PRICE_TYPE_COIN  = 2; // コイン

    /**
     * ファイル名取得
     * @return string
     */
    protected static function getFileName()
    {
        return 'gacha_master.php';
    }

    /**
     * ガチャBeanリスト
	 * @var mixed
     */
    private static $gacha_bean_list = null;

    /**
     * ガチャID有効判定
     * @param int $gacha_id
     * @return bool
     */
    public static function isValid(int $gacha_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id]);
        $GachaBean = self::getGachaBean($gacha_id);
        if (is_null($GachaBean) === true) {
            $is_valid = false;
        } else {
            $is_valid = true;
        }
        \AppLogger::endFunc(__METHOD__, $is_valid);
        return $is_valid;
    }

    /**
     * ガチャBeanリスト取得
	 * @return array
     */
    public static function getGachaBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null(self::$gacha_bean_list) === true) {
            self::setGachaBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return self::$gacha_bean_list;
    }

    /**
     * 指定ガチャBean取得
     * @param int $gacha_id
	 * @return \Beans\MasterData\GachaBean
     */
    public static function getGachaBean(int $gacha_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id]);
        $GachaBean       = null;
        $gacha_bean_list = self::getGachaBeanList();
        if (is_null($gacha_bean_list) === false && array_key_exists($gacha_id, $gacha_bean_list) === true) {
            $GachaBean = $gacha_bean_list[$gacha_id];
        }
        \AppLogger::endFunc(__METHOD__);
        return $GachaBean;
    }

    /**
     * ガチャBeanリストセット
     */
    private static function setGachaBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        foreach (parent::getAll() as $master) {
            self::$gacha_bean_list[$master['gacha_id']] = new GachaBean($master);
        }
        \AppLogger::endFunc(__METHOD__);
    }

}
