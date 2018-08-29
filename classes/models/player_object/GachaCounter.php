<?php

namespace PlayerObject;

use Beans\Db\GachaCounterBean;
use Db\PlayerDb\GachaCounterTbl;

Class GachaCounter extends PlayerObject
{

     /**
     * ガチャカウンターBeanリスト
     * @var array
     */
    private $gacha_counter_bean_list = null;

    /**
     * 指定ガチャID、（抽選回数）の総実行回数取得
     * @param int $gacha_id
     * @param int $gacha_menu_id
     * @return int
     */
    public function getTotalPlay(int $gacha_id, int $gacha_menu_id = null)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id, '$gacha_menu_id' => is_null($gacha_menu_id) ? 'null' : $gacha_menu_id]);
        $total_play = 0;
        if (count($this->getGachaCounterBeanList()) > 0) {
            foreach ($this->getGachaCounterBeanList() as $gacha_counter_bean_list) {
                foreach ($gacha_counter_bean_list as $GachaCounterBean) {
                    // ガチャIDチェック
                    if ($GachaCounterBean->getGachaId() != $gacha_id) {
                        continue;
                    }
                    //  抽選回数チェック
                    if (is_null($gacha_menu_id) === false && $GachaCounterBean->getGachaMenuId() != $gacha_menu_id) {
                        continue;
                    }
                    $total_play += $GachaCounterBean->getTotalPlay();
                }
            }
        }
        \AppLogger::endFunc(__METHOD__, $total_play);
        return $total_play;
    }

    /**
     * 指定ガチャID、抽選回数の日別実行回数取得
     * @param int $gacha_id
     * @param int $gacha_menu_id
     * @return int
     */
    public function getDailyPlay(int $gacha_id, int $gacha_menu_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id, '$gacha_menu_id' => $gacha_menu_id]);
        $daily_play       = 0;
        $GachaCounterBean = $this->getGachaCounterBean($gacha_id, $gacha_menu_id);
        if (is_null($GachaCounterBean) === false) {
            $daily_play = $GachaCounterBean->getDailyPlay();
        }
        \AppLogger::endFunc(__METHOD__, $daily_play);
        return $daily_play;
    }

    /**
     * ガチャ実行回数をインクリメント
     * @param int $gacha_id
     * @param int $gacha_menu_id
     */
    public function incrGachaPlayCount(int $gacha_id, int $gacha_menu_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id, '$gacha_menu_id' => $gacha_menu_id]);
        // IDチェック
        if (GachaMaster::isValid($gacha_id) === false) {
            throw new \Exception();
        }
        // Bean更新
        $GachaCounterBean = $this->getGachaCounterBean($gacha_id, $gacha_menu_id);
        if (is_null($GachaCounterBean) === true) {
            $GachaCounterBean = new GachaCounterBean([
                'player_seq_num' => $this->player_seq_num,
                'gacha_id'       => $gacha_id,
                'gacha_menu_id'  => $gacha_menu_id,
                'play_total'     => 1,
                'play_daily'     => 1,
                'created_at'     => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
                'updated_at'     => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            ]);
            $this->gacha_counter_bean_list[$gacha_id][$gacha_menu_id] = $GachaCounterBean;
        } else {
            $GachaCounterBean->setTotalPlay($GachaCounterBean->getTotalPlay() + 1);
            $GachaCounterBean->setDailyPlay($GachaCounterBean->getDailyPlay() + 1);
            $GachaCounterBean->setUpdateFlg(true);
        }
        $this->gacha_counter_bean_list[$gacha_id] = $GachaCounterBean;
        // DB更新
        $GachaCounterTbl = \Db\Factory::getInstance(GachaCounterTbl::class, $this->player_seq_num);
        $GachaCounterTbl->insertOrUpdate([$GachaCounterBean->toRecord()]);
        \AppLogger::endFunc(__METHOD__);
    }

    public function syncdb()
    {

    }

    /**
     * ガチャカウンターBeanリスト取得
	 * @return array
     */
    public function getGachaCounterBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->gacha_counter_bean_list) === true) {
            $this->setGachaCounterBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->gacha_counter_bean_list;
    }

    /**
     * 指定ガチャカウンターBean取得
     * @param int $gacha_id
     * @param int $gacha_menu_id
	 * @return \Beans\GachaCounterBean
     */
    public function getGachaCounterBean(int $gacha_id, int $gacha_menu_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$gacha_id' => $gacha_id, '$gacha_menu_id' => $gacha_menu_id]);
        $GachaCounterBean        = null;
        $gacha_counter_bean_list = $this->getGachaCounterBeanList();
        if (count($gacha_counter_bean_list) > 0
         && array_key_exists($gacha_id, $gacha_counter_bean_list) === true
         && array_key_exists($gacha_menu_id, $gacha_counter_bean_list[$gacha_id]) === true) {
            $GachaCounterBean = $gacha_counter_bean_list[$gacha_id][$gacha_menu_id];
        }
        \AppLogger::endFunc(__METHOD__);
        return $GachaCounterBean;
    }

    /**
     * ガチャカウンターBeanリストセット
     */
    private function setGachaCounterBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        $GachaCounterTbl = \Db\Factory::getInstance(GachaCounterTbl::class, $this->player_seq_num);
        $record_list     = $GachaCounterTbl->searchBy(['player_seq_num' => $this->player_seq_num]);
        if (count($record_list) > 0) {
            foreach ($record_list as $record) {
                $this->gacha_counter_bean_list[$record['gacha_id']][$record['gacha_menu_id']] = new GachaCounterBean($record);
            }
        } else {
            $this->gacha_counter_bean_list = [];
        }
        \AppLogger::endFunc(__METHOD__);
    }

}