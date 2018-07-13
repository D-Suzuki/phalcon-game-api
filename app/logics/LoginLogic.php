<?php

use GameObject\Result\Login\LoginResult;
use PlayerObject\Login;
use PlayerObject\GiftBox;

Class LoginLogic
{

    /**
     * ログイン処理
     * @param \PlayerObject\Login $Login
     * @param \PlayerObject\GiftBox $GiftBox
     * @return \GameLogic\Login\Result\LoginResult
     */
    public static function login($player_seq_num)
    {
        // 結果オブジェクト初期化
        $LoginResult = new LoginResult();

        // 必要プレイヤーオブジェクト生成
        $Login     = PlayerObject::getInstance($player_seq_num, Login::class);
        $RewardBox = PlayerObject::getInstance($player_seq_num, RewardBox::class);

        if ($Login->isFirstAtToday() === true) {
            // 当日最初のログイン処理
        }

        // 当日ログボ受取済み判定
        $login_bonus_bean_list = $Login->getTodayLoginBonusBeanList();
        foreach ($login_bonus_bean_list as $LoginBonusBean) {
            if ($LoginBonusBean->isReceived() === false) {
                $RewardBox->stackReward($LoginBonusBean->getReward()); // 配送前報酬を積む
                $Login->toReceived($LoginBonusBean);                   // 受取済みにする
                $LoginResult->addLoginBonusBean($LoginBonusBean);      // 結果オブジェクトにログボ情報セット
            }
        }
        // ギフトBOXへ配送
        $GiftBox->deliver();

        // 最終ログイン日時更新
        $Login->stamp();

        return $LoginResult;
    }

}