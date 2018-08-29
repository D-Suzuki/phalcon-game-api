<?php

namespace Logics;

use GameObject\Reward;
use GameObject\Result\Player\CreateResult;
use GameObject\Result\Player\AddExpResult;
use GameObject\Result\Player\AuthResult;
use GameObject\Result\Player\RenameResult;
use GameObject\Sequence\PlayerSequence;
use PlayerObject\PlayerObject;
use PlayerObject\Player;
use PlayerObject\Profile;
use PlayerObject\Jewel;
use PlayerObject\Coin;

Class PlayerLogic
{

    /**
     * クライアント用ステータスデータ取得
     * @param int $player_seq_num
     * @return array
     */
    public static function getPlayerForClient(int $player_seq_num): array
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);
        /* @var $Player  \PlayerObject\Player */
        $Player = PlayerObject::getInstance($player_seq_num, Player::class);
        $player_for_client = [
            'exp'   => $Player->getPlayerBean()->getExp(),
            'level' => $Player->getPlayerBean()->getLevel(),
        ];
        AppLogger::endFunc(__METHOD__);
        return $player_for_client;
    }

    /**
     * プレイヤー生成
     */
    public static function createPlayer(string $nickname) :CreateResult
    {
        AppLogger::startFunc(__METHOD__);

        // プレイヤーシーケンスNUM生成
        $player_seq_num = PlayerSequcence::getNext();
        $CreateResult   = new CreateResult($player_seq_num);

        // 必要インスタンス生成
        $Player        = PlayerObject::getInstance($player_seq_num, Player::class);        /* @var $Player        \PlayerObject\Player        */
        $PlayerAuth    = PlayerObject::getInstance($player_seq_num, PlayerAuth::class);    /* @var $PlayerAuth    \PlayerObject\PlayerAuth    */
        $Profile       = PlayerObject::getInstance($player_seq_num, Profile::class);       /* @var $Profile       \PlayerObject\Profile       */
        $Jewel         = PlayerObject::getInstance($player_seq_num, Jewel::class);         /* @var $Jewel         \PlayerObject\Jewel         */
        $Coin          = PlayerObject::getInstance($player_seq_num, Coin::class);          /* @var $Coin          \PlayerObject\Coin          */
        $Life          = PlayerObject::getInstance($player_seq_num, Life::class);          /* @var $Life          \PlayerObject\Life          */
        $FriendCounter = PlayerObject::getInstance($player_seq_num, FriendCounter::class); /* @var $FriendCounter \PlayerObject\FriendCounter */

        // 各種データを初期化
        $Player->intialize();
        $PlayerAuth->intialize();
        $Profile->intialize($nickname);
        $Jewel->intialize();
        $Coin->intialize();
        $Life->intialize();
        $FriendCounter->intialize();

        // 認証情報を結果に保存
        $CreateResult->setAuthKey($PlayerAuth->getPlayerAuthBean()->getAuthKey());
        $CreateResult->setPassword($PlayerAuth->getPlayerAuthBean()->getPassword());

        $CreateResult->setResultCode(CreateResult::COMPLETE);
        AppLogger::endFunc(__METHOD__);
        return $CreateResult;
    }

    /**
     * プレイヤー認証
     * @param string $auth_key
     * @param string $password
     * @return AuthResult
     */
    public static function auth(string $auth_key, string $password): AuthResult
    {
        AppLogger::startFunc(__METHOD__, ['$auth_key' => $auth_key, '$password' => $password]);

        $AuthResult = new AuthResult();

        // 認証チェック
        $PlayerAuthBean = PlayerAuth::getPlayerAuthBean($auth_key, $password);
        if (is_null($PlayerAuthBean) === true) {
            $AuthResult->setResultCode(AuthResult::ERROR);
            return $AuthResult;
		}

		// 認証成功
		$session_key = self::createSession($PlayerAuthBean->getPlayerSeqNum());
		$AuthResult->setPlayerSeqNum($PlayerAuthBean->getPlayerSeqNum());
		$AuthResult->setSessionKey($session_key);
		$AuthResult->setResultCode(AuthResult::COMPLETE);

        AppLogger::endFunc(__METHOD__);
        return $AuthResult;
    }

    /**
     * 経験値付与処理
     * @param int $player_seq_num
     * @param int $add_exp
     * @return AddExpResult
     */
    public static function addExp(int $player_seq_num, int $add_exp, int $scene_id): AddExpResult
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$add_exp' => $add_exp]);
        // 結果インスタンス初期化
        $AddExpResult = new AddExpResult($player_seq_num, $add_exp, $scene_id);

        // プレイヤーインスタンス生成
        $Player = PlayerObject::getInstance($player_seq_num, Player::class);

        $AddExpResult->setBefLevel($Player->getPlayerBean()->getLevel()); // 追加前レベル保存
		$Player->addExp($add_exp);                                        // 経験値追加
        $AddExpResult->setAftLevel($Player->getPlayerBean()->getLevel()); // 追加後レベル保存

        // レベルアップ時処理
        if ($AddExpResult->isLevelUp() === true) {
            // ▼ スタミナ全回復
            $Life = PlayerObject::getInstance($player_seq_num, Life::class);
            $Life->fullRecorvery(AppConst::SCENE_ID_PLAYER_LEVEL_UP);
            // ▼ レベルアップ報酬
            $GiftBox = PlayerObject::getInstance($player_seq_num, GiftBox::class);
            for ($up_level = $AddExpResult->getBefLevel() + 1; $up_level <= $AddExpResult->getAftLevel(); $up_level++) {
                $Reward = PlayerExpMaster::getReward($up_level);
                $GiftBox->stackReward($Reward);
            }
            $GiftBox->deliver();
        }

        // 結果コードセット & 履歴追加
        $AddExpResult->setResultCode(AddExpResult::COMPLETE);
        $AddExpResult->createHistory();
        AppLogger::endFunc(__METHOD__);
        return $AddExpResult;
    }

    /**
     * セッション生成
     * @param string $auth_key
     * @param string $password
     * @return CreateSessionResult
     */
    private static function createSession(string $player_seq_num): String
    {
        AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);

        // セッションキー生成
        $session_key = md5(microtime() . $player_seq_num);

        // Memcacheに乗せる
        Memcache::set($session_key, $player_seq_num);
        Memcache::set($player_seq_num, $session_key); // 機種移行の際、移行元のセッション削除のために必要

        AppLogger::endFunc(__METHOD__);
        return $session_key;
    }

}
