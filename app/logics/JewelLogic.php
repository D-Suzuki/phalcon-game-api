<?php

use PlayerObject\PlayerObject;
use PlayerObject\Jewel;
use GameObject\Result\Jewel\UseJewelResult;

Class JewelLogic
{

    const USE_TYPE_NORMAL      = 1;
    const USE_TYPE_FREE_ONLY   = 2;
    const USE_TYPE_CHARGE_ONLY = 3;

    /**
     * クライアント用ジュエルデータ取得
     * @param int $player_seq_num
     * @return array
     */
    public static function getJewelDataForClient(int $player_seq_num)
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);

        /* @var $Jewel \PlayerObject\Jewel */
        $Jewel = PlayerObject::getInstance($player_seq_num, Jewel::class);

        $jewel_data_for_client = [
            'free_jewel'   => (int) $Jewel->getFreeJewel(),
            'charge_jewel' => (int) $Jewel->getChargeJewel(),
        ];

        AppLogger::endFunc(__METHOD__);
        return $jewel_data_for_client;
    }

    /**
     * ジュエル使用
     * @param int $player_seq_num
     * @param int $price
     * @param int $use_type
     * @param int $scene_id
     */
    public static function useJewel(int $player_seq_num, int $use_count, int $use_type, int $scene_id)
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$use_count' => $use_count, '$use_type' => $use_type, '$scene_id' => $scene_id]);

        $UseJewelResult = new UseJewelResult($player_seq_num, $use_count, $scene_id);

        /* @var $Jewel \PlayerObject\Jewel */
        $Jewel = PlayerObject::getInstance($player_seq_num, Jewel::class);
        if ($Jewel->hasJewel($use_count) === true) {
            $Jewel->decrJewel($use_count);
            $Jewel->syncdb();
        } else {
            $UseJewelResult->setResultCode(UseJewelResult::IS_NOT_ENOUGH);
            AppLogger::endFunc(__METHOD__);
            return $UseJewelResult;
        }

        $UseJewelResult->setResultCode(UseJewelResult::COMPLETE);
        $UseJewelResult->setUsedFreeJewel($Jewel->getUsedFreeJewel());
        $UseJewelResult->setUsedChargeJewel($Jewel->getUsedChargeJewel());
        $UseJewelResult->createHistory();
        AppLogger::endFunc(__METHOD__);
        return $UseJewelResult;
    }

}