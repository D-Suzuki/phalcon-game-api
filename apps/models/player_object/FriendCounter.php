<?php

namespace PlayerObject;

use Beans\Db\FriendCounterBean;
use Db\ShareDb\FriendCounterTbl;

Class FriendCounter extends PlayerObject
{

    const MAX_FRIEND_COUNT = 30;

    /**
     * 全体のフレンドカウンターリスト（ロック用）
     * @var array
     */
    private static $locked_friend_counter_list = [];

    /**
     * フレンド情報テーブルのロックセレクト
     * @param array $player_seq_num_list
     * @throws Exception
     */
    public static function lockFriendCounter(array $player_seq_num_list)
    {
        \AppLogger::startFunc(__METHOD__, $player_seq_num_list);
        $FriendCounterTbl = \Db\Factory::getInstance(FriendCounterTbl::class);
        $record_list      = $FriendCounterTbl->selectForUpdate($player_seq_num_list);
        if (empty($record_list) === true) {
            throw \Exception();
        }
        foreach ($record_list as $record) {
            self::$locked_friend_counter_list[$record['player_seq_num']] = $record;
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * 初期フレンドカウンターデータ作成
     */
    public function initilize()
    {
        \AppLogger::startFunc(__METHOD__);
        // Bean追加
        $FriendCounterBean = new FriendCounterBean([
            'player_seq_num' => $this->player_seq_num,
            'friend_count'   => 0,
            'request_count'  => 0,
            'created_at'     => AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            'updated_at'     => AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
        ]);
        $this->FriendCounterBean = $FriendCounterBean;
        // DB追加
        $FriendCounterTbl = \Db\Factory::getInstance(FriendCounterTbl::class);
        $FriendCounterTbl->insert($FriendCounterBean->toRecord());
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * フレンド情報Bean
     * @var FriendCounterBean
     */
    private $FriendCounterBean = null;

    /**
     * フレンド数満タン判定
     * @return bool
     */
    public function isFullFriend()
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendCounterBean = $this->getFriendCounterBean();
        if ($FriendCounterBean->getFriendCount() < self::MAX_FRIEND_COUNT) {
            $is_full_friend = false;
        } else {
            $is_full_friend = true;
        }
        \AppLogger::endFunc(__METHOD__, $is_full_friend);
        return $is_full_friend;
    }

    /**
     * 申請数満タン判定
     * @return bool
     */
    public function isFullRequest()
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendCounterBean = $this->getFriendCounterBean();
        if ($FriendCounterBean->getFriendCount() + $FriendCounterBean->getRequestCount() < self::MAX_FRIEND_COUNT) {
            $is_full_request = false;
        } else {
            $is_full_request = true;
        }
        \AppLogger::endFunc(__METHOD__, $is_full_request);
        return $is_full_request;
    }

    /**
     * フレンド数インクリメント
     */
    public function incrFriend(bool $db_update_flg = true)
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendCounterBean = $this->getFriendCounterBean();
        $FriendCounterBean->setFriendCount($FriendCounterBean->getFriendCount() + 1);
        $FriendCounterBean->setUpdateFlg(true);
        if ($db_update_flg === true) {
            $FriendCounterTbl = \Db\Factory::getInstance(FriendCounterTbl::class, $this->player_seq_num);
            $FriendCounterTbl->insertOrUpdate([$FriendCounterBean->toRecord()]);
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * フレンド数デクリメント
     */
    public function decrFriend(bool $db_update_flg = true)
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendCounterBean = $this->getFriendCounterBean();
        $FriendCounterBean->setFriendCount($FriendCounterBean->getFriendCount() - 1);
        $FriendCounterBean->setUpdateFlg(true);
        if ($db_update_flg === true) {
            $FriendCounterTbl = \Db\Factory::getInstance(FriendCounterTbl::class, $this->player_seq_num);
            $FriendCounterTbl->insertOrUpdate([$FriendCounterBean->toRecord()]);
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * 申請数インクリメント
     */
    public function incrRequest(bool $db_update_flg = true)
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendCounterBean = $this->getFriendCounterBean();
        $FriendCounterBean->setRequestCount($FriendCounterBean->getRequestCount() + 1);
        $FriendCounterBean->setUpdateFlg(true);
        if ($db_update_flg === true) {
            $FriendCounterTbl = \Db\Factory::getInstance(FriendCounterTbl::class, $this->player_seq_num);
            $FriendCounterTbl->insertOrUpdate([$FriendCounterBean->toRecord()]);
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * 申請数デクリメント
     */
    public function decrRequest(bool $db_update_flg = true)
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendCounterBean = $this->getFriendCounterBean();
        $FriendCounterBean->setRequestCount($FriendCounterBean->getRequestCount() - 1);
        $FriendCounterBean->setUpdateFlg(true);
        if ($db_update_flg === true) {
            $FriendCounterTbl = \Db\Factory::getInstance(FriendCounterTbl::class, $this->player_seq_num);
            $FriendCounterTbl->insertOrUpdate([$FriendCounterBean->toRecord()]);
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * フレンド情報Bean取得
     * @return FriendCounterBean
     */
    private function getFriendCounterBean()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->FriendCounterBean) === true) {
            $this->setFriendCounterBean();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->FriendCounterBean;
    }

    /**
     * フレンドカウンターBean取得
     * @return \Beans\FriendCounterBean
     */
    private function setFriendCounterBean()
    {
        \AppLogger::startFunc(__METHOD__);
        if (isset(self::$locked_friend_counter_list[$this->player_seq_num]) === true) {
            $record = self::$locked_friend_counter_list[$this->player_seq_num];
        } else {
            $FriendCounterTbl = \Db\Factory::getInstance(FriendCounterTbl, $this->player_seq_num);
            $record           = $FriendCounterTbl->findByPk($this->player_seq_num);
        }
        $this->FriendCounterBean = new FriendCounterBean($record);
        \AppLogger::endFunc(__METHOD__);
    }

}