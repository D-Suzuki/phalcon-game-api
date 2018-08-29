<?php

namespace Logics;

use PlayerObject\PlayerObject;
use PlayerObject\Coin;


Class CoinLogic
{

    /**
     * クライアント用コインデータ取得
     * @param int $player_seq_num
     * @return array
     */
    public static function getCoinDataForClient(int $player_seq_num) : array
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);

        /* @var $Coin \PlayerObject\Coin */
        $Coin = PlayerObject::getInstance($player_seq_num, Coin::class);

        $coin_data_for_client = [
            'total_coin' => (int) $Coin->getTotalCount(),
        ];

        AppLogger::endFunc(__METHOD__);
        return $coin_data_for_client;
    }

    /**
     * コイン使用
     * @param int $player_seq_num
     * @param int $price
     * @param GameObject\Result\Coin\UserCoinResult
     */
    public static function useCoin(int $player_seq_num, int $use_count, int $scene_id) : UseCoinResult
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$use_count' => $use_count, '$scene_id' => $scene_id]);

        $UseCoinResult = new UseCoinResult($player_seq_num, $use_count, $scene_id);

        /* @var $Coin \PlayerObject\Coin */
        $Coin = PlayerObject::getInstance($player_seq_num, Coin::class);

        if ($Coin->hasCoin($use_count) === false) {
            $UseCoinResult->setResultCode(UseCoinResult::IS_NOT_ENOUGH);
            AppLogger::endFunc(__METHOD__);
            return $UseCoinResult;
        }

        $Coin->decrCoin($use_count);
        $Coin->syncdb();
        $UseCoinResult->setResultCode(UserCoinResult::COMPLETE);

        $UseCoinResult->createHistory();
        AppLogger::endFunc(__METHOD__);
        return $UseCoinResult;
    }

}
