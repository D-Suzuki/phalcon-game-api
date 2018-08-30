<?php

namespace Logics;

use Master\AchiveMaster;
use Result\Achive\ClearResult;
use PlayerObject\AchiveClear;
use Logger\AppLogger;

Class AchiveLogic
{

    /**
     * クライアント用アチーブリスト取得
     * @param int $player_seq_num
     * @return array
     */
    public static function getAchiveListForClient(int $player_seq_num)
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);
        $achive_list_for_client = [];

        $achive_bean_list = AchiveMaster::getAchiveBeanList();
        if (count($achive_bean_list) > 0) {
            $AchiveClear = PlayerObject::getInstance($player_seq_num, AchiveClear::class);
            foreach ($achive_bean_list as $AchiveBean) {
                if ($AchiveClear->isCleared($AchiveBean->getAchiveId())) {
                    continue; // クリア済みは非表示
                }
                if ($AchiveClear->isCleared($AchiveBean->getRequiredClearAchiveId()) === false) {
                    continue; // 必要クリアアチーブ未クリア時は非表示
                }
                $achive_list_for_client[] = [
                    'achive_id'      => $AchiveBean->getAchiveBean(),
                    'achive_type'    => $AchiveBean->getAchiveType(),
                    'required_value' => $AchiveBean->getRequiredValue(),
                ];
            }
        }

        AppLogger::endFunc(__METHOD__);
        return $achive_list_for_client;
    }

    /**
     * クライアント用ガチャリスト取得
     * @param int $player_seq_num
     * @param int $achive_id
     * @return array
     */
    public static function clearAchive(int $player_seq_num, int $achive_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$achive_id' => $achive_id]);

        // 必要インスタンス生成
        $ClearResult = new ClearResult($player_seq_num, $achive_id);
        $AchiveClear = PlayerObject::getInstance($player_seq_num, AchiveClear::class);
        $GiftBox     = PlayerObject::getInstance($player_seq_num, GiftBox::class);

        /*                                                  ◆ 各種チェック
          ================================================================= */
        // IDチェック
        $AchiveBean = AchiveMaster::getAchiveBean($achive_id);
        if (is_null($AchiveBean) === true) {
            throw new \Exception();
        }
        // クリア済判定
        if ($AchiveClear->isCleared($achive_id) === true) {
            $ClearResult->setResult(ClearResult::ALREADY_CLEARED);
            \AppLogger::endFunc(__METHOD__);
            return $ClearResult;
        }
        // クリア判定
        if (self::isClear($player_seq_num, $achive_id) === false) {
            $ClearResult->setResult(ClearResult::IS_NOT_CLEAR);
            \AppLogger::endFunc(__METHOD__);
            return $ClearResult;
        }

        /*                                                   ◆ クリア処理
          ================================================================= */
        // クリアデータ追加
        $AchiveClear->addClearAchive($achive_id);
        // ギフトBOXへ報酬贈る
        $Reward = Reward::createReward($AchiveBean->getItemId(), $AchiveBean->getItemCount());
        $GiftBox->deliver($Reward);
        // 結果インスタンスセット
        $ClearResult->setResultCode(ClearResult::COMPLETE);
        $ClearResult->setReward($Reward);

        \AppLogger::endFunc(__METHOD__);
        return $ClearResult;
    }

    /**
     * アチーブクリア判定
     * @param int $player_seq_num
	 * @param int $achive_id
	 * @return boolean
     */
    private static function isClear(int $player_seq_num, int $achive_id)
    {

	}

}
