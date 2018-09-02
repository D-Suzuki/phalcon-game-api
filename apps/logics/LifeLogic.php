<?php

namespace Logics;

use PlayerObject\PlayerObject;
use PlayerObject\Life;
use GameObject\Result\Life\UseLifeResult;

Class LifeLogic
{

    /**
     * クライアント用ライフデータ取得
     * @param int $player_seq_num
     * @return array
     */
    public static function getLifeForClient(int $player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);
        $life_for_client = [];
        /* @var $Life \PlayerObject\Life */
        $Life = PlayerObject::getInstance($player_seq_num, Life::class);
        var_dump($Life);exit;
        $life_for_client = [
            'life_count'         => (int) $Life->getLifeBean()->getCurrentLife(),
            'next_recovery_time' => (int) $Life->getLifeBean()->getNextRecoveryTime(),
        ];
        \AppLogger::endFunc(__METHOD__);
        return $life_for_client;
    }

    /**
     * ライフ使用
     * @param int $player_seq_num
     * @param int $use_count
     * @param int $scene_id
     * @return UseLifeResult
     */
    public static function useLife(int $player_seq_num, int $use_count, int $scene_id) : UseLifeResult
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$use_count' => $use_count, '$scene_id' => $scene_id]);

        $UseLifeResult = new UseLifeResult($player_seq_num, $use_count, $scene_id);

        /* @var $Life \PlayerObject\Life */
        $Life = PlayerObject::getInstance($player_seq_num, Life::class);
        if ($Life->hasLife($use_count) === true) {
            $Life->decrLife($use_count);
            $Life->syncdb();
        } else {
            $UseLifeResult->setResultCode(UseLifeResult::COMPLETE);
            \AppLogger::endFunc(__METHOD__);
            return $UseLifeResult;
        }

        $UseLifeResult->setResultCode(UseLifeResult::COMPLETE);
        $UseLifeResult->createHistory();
        \AppLogger::endFunc(__METHOD__);
        return $UseLifeResult;
    }

    /**
     * ライフ追加
     * @param int $player_seq_num
     * @param int $add_count
     * @param int $scene_id
     * @return AddLifeResult
     */
    public static function incrLife(int $player_seq_num, int $add_count, int $scene_id) : AddLifeResult
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$add_count' => $add_count, '$scene_id' => $scene_id]);

        $AddLifeResult = new AddLifeResult($player_seq_num, $add_count, $scene_id);

        \AppLogger::endFunc(__METHOD__);
        return $AddLifeResult;
    }

}
