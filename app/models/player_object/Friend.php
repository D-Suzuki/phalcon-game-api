<?php

namespace PlayerObject;

use \Beans\Db\FriendBean;
use \Db\PlayerDb\FriendTbl;

Class Friend extends PlayerObject
{

    const STATUS_FRIEND = 1;

    /**
     * フレンドBeanリスト
     * @var array
     */
    private $friend_bean_list = null;

    /**
     * フレンド判定
     * @return bool
     */
    public function isFriend(int $friend_player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['$friend_player_seq_num' => $friend_player_seq_num]);
        $FriendBean = $this->getFriendBean($friend_player_seq_num);
        if (is_null($FriendBean) === true) {
            $is_friend = false;
        } else {
            $is_friend = true;
        }
        \AppLogger::endFunc(__METHOD__, $is_friend);
        return $is_friend;
    }

    /**
     * フレンド追加
     * @param int $friend_player_seq_num
     * @throws Exception
     */
    public function addFriend(int $friend_player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['$friend_player_seq_num' => $friend_player_seq_num]);
        if ($this->isFriend() === true) {
            throw new \Exception();
        }
        // Bean追加
        $FriendBean = new FriendBean([
           'player_seq_num'        => $this->player_seq_num,
           'friend_player_seq_num' => $friend_player_seq_num,
           'status'                => Friend::STATUS_FRIEND,
           'created_at'            => AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
           'updated_at'            => AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
        ]);
        $this->friend_bean_list[$friend_player_seq_num] = $FriendBean;
        // DB更新
        $FriendTbl = \Db\Factory::getInstance(FriendTbl);
        $FriendTbl->insertOrUpdate([$FriendBean->toRecord()]);
        \AppLogger::endFunc(__METHOD__);
	}

    /**
     * フレンド更新
     * @param int $friend_player_seq_num
     * @throws Exception
     */
    public function updateFriend(int $friend_player_seq_num, int $status)
    {
        \AppLogger::startFunc(__METHOD__, ['$friend_player_seq_num' => $friend_player_seq_num, 'status' => $status]);
        $FriendBean = $this->getFriendBean($friend_player_seq_num);
        if (is_null($FriendBean) === true) {
            throw new \Exception();
        }
        $FriendBean-setStatus($status);
        $FriendBean->setUpdateFlg(true);
        $FriendTbl = \Db\Factory::getInstance(FriendTbl);
        $FriendTbl->insertOrUpdate([$FriendBean->toRecord()]);
        \AppLogger::endFunc(__METHOD__);
	}

    /**
     * フレンドBeanリスト取得
     * @return array
     */
    private function getFriendBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->friend_bean_list) === true) {
            $this->setFriendBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->friend_bean_list;
    }

    /**
     * 指定フレンドBean取得
     * @return FriendBean
     */
    private function getFriendBean(int $friend_player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['$friend_player_seq_num' => $friend_player_seq_num]);
        $FriendBean       = null;
        $friend_bean_list = $this->getFriendBeanList();
        if (count($friend_bean_list) > 0 && array_key_exists($friend_player_seq_num, $friend_bean_list) === true) {
            $FriendBean = $friend_bean_list[$friend_player_seq_num];
        }
        \AppLogger::endFunc(__METHOD__);
        return $FriendBean;
    }

    /**
     * フレンドBeanリストセット
     */
    private function setFriendBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        $FriendTbl   = \Db\Factory::getInstance(FriendTbl::class, $this->player_seq_num);
        $record_list = $FriendTbl->searchBy(['player_seq_num' => $this->player_seq_num, 'status' => self::STATUS_FRIEND]);
        if (count($record_list) > 0) {
            foreach ($record_list as $record) {
                $this->friend_bean_list[$record['friend_player_seq_num']] = new FriendBean($record);
    		}
        } else {
            $this->friend_bean_list = [];
        }
		\AppLogger::endFunc(__METHOD__);
    }

}