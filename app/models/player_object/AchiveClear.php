<?php

namespace PlayerObject;

use Beans\Db\AchiveBean;
use MasterData\AchiveMaster;

Class AchiveClear extends PlayerObject
{

     /**
     * アチーブクリアBeanリスト
	 * @var mixed
     */
    private $achive_clear_bean_list = null;

    /**
     * クリア済み判定
     * @param int $achive_id
     * @return bool
     */
    public function isCleared(int $achive_id)
    {
        \AppLogger::startFunct(__METHOD__, ['$achive_id' => $achive_id]);
        if (is_null($this->getAchiveClearBean($achive_id)) === true) {
            $is_cleared = false;
        } else {
            $is_cleared = true;
        }
        \AppLogger::endFunc(__METHOD__);
        return $is_cleared;
    }

    /**
     * クリア状態アチーブを追加
     * @param int $achive_id
     */
    public function addClearAchive(int $achive_id)
    {
        \AppLogger::startFunct(__METHOD__, ['$achive_id' => $achive_id]);
        // IDチェック
        if (AchiveMaster::isValid($achive_id) === false) {
            throw new \Exception();
        }
        // クリア済チェック
        if ($this->isCleared($achive_id) === true) {
            throw new \Exception();
        }
        // Bean追加
        $AchiveClearBean = new AchiveClearBean([
            'player_seq_num' => $this->player_seq_num,
            'achive_id'      => $achive_id,
            'created_at'     => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            'updated_at'     => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
        ]);
        $this->achive_clear_bean_list[$achive_id] = $AchiveClearBean;
        // DB更新
        $AchiveClearTbl = \Db\Factory::getInstance(AchiveClearTbl::class, $this->player_seq_num);
        $AchiveClearTbl->insertOrUpdate([$AchiveClearBean->toRecord()]);
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * アチーブクリアBeanリスト取得
	 * @return array
     */
    public function getAchiveClearBeanList()
    {
        \AppLogger::startFunct(__METHOD__);
        if (is_null($this->achive_clear_bean_list) === true) {
            $this->setAchiveClearBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->achive_clear_bean_list;
    }

    /**
     * 指定アチーブクリアBean取得
     * @param int $achive_id
	 * @return \Beans\AchiveClearBean
     */
    public function getAchiveClearBean(int $achive_id)
    {
        \AppLogger::startFunct(__METHOD__, ['$achive_id' => $achive_id]);
        $AchiveClearBean        = null;
        $achive_clear_bean_list = $this->getAchiveClearBeanList();
        if (is_null($achive_clear_bean_list) === false && array_key_exists($achive_id, $achive_clear_bean_list) === true) {
            $AchiveClearBean = $achive_clear_bean_list[$achive_id];
        }
        \AppLogger::endFunc(__METHOD__);
        return $AchiveClearBean;
    }

    /**
     * アチーブクリアBeanリストセット
     */
    private function setAchiveClearBeanList()
    {
        \AppLogger::startFunct(__METHOD__);
        $AchiveClearTbl = \Db\Factory::getInstance(AchiveClearTbl::class, $this->player_seq_num);
        $record_list    = $AchiveClearTbl->searchBy(['player_seq_num' => $this->player_seq_num]);
        if (count($record_list) > 0) {
            foreach ($record_list as $record) {
                $this->achive_clear_bean_list[$record['achive_id']] = new $AchiveClearBean($record);
            }
        } else {
            $this->achive_clear_bean_list = [];
        }
        \AppLogger::endFunc(__METHOD__);
    }

}