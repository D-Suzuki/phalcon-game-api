<?php

use GameObject\Result\Player\RenameResult;
use GameObject\NgWord;
use PlayerObject\PlayerObject;
use PlayerObject\Profile;

Class ProfileLogic
{

    /**
     * クライアント用プロフィールデータ取得
     * @param int $player_seq_num
     * @return array
     */
    public static function getProfileForClient(int $player_seq_num)
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);
        /* @var $Profile \PlayerObject\Profile */
        $Profile = PlayerObject::getInstance($player_seq_num, Profile::class);
        $prifile_for_client = [
            'open_id'  => $Profile->getProfileBean()->getOpenId(),
            'nickname' => $Profile->getProfileBean()->getNickname(),
        ];
        AppLogger::endFunc(__METHOD__);
        return $prifile_for_client;
    }

    /**
     * 経験値付与処理
     * @param int $player_seq_num
     * @param string $nickname
     * @return RenameResult
     */
    public static function rename(int $player_seq_num, string $nickname)
    {
        \AppLogger::startFunc(__METHOD__, ['player_seq_num' => $player_seq_num, 'nickname' => $nickname]);

        // 結果オブジェクト初期化
        $RenameResult = new RenameResult();

        // 文字数チェック
        if (strlen($nickname) > Profile::MAX_LENGTH_NICKNAME) {
            $RenameResult->setPlayer(RenameResult::LENGTH_ERROR);
            \AppLogger::endFunc(__METHOD__);
            return $RenameResult;
        }

        // NGワードチェック
        if (NgWord::isPassed($nickname) === false) {
            $RenameResult->setPlayer(RenameResult::NG_WORD_ERROR);
            \AppLogger::endFunc(__METHOD__);
            return $RenameResult;
        }

        // プロフィールオブジェクト生成
        $Profile = PlayerObject::getInstance($player_seq_num, Profile::class);
		$Profile->rename($nickname);

        $RenameResult->setResultCode(RenameResult::COMPLETE);
        \AppLogger::endFunc(__METHOD__);
        return $RenameResult;
    }

}