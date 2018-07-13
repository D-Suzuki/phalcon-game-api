<?php

namespace Traits\BeanParts;

Trait FriendPlayerSeqNum
{

    /**
     * フレンドプレイヤーシーケンスNUM
     * @var int
     */
    protected $friend_player_seq_num = null;

    /**
     * フレンドプレイヤーシーケンスNUMセット
     * @param int $friend_player_seq_num
     */
    public function setFriendPlayserSeqNum(int $friend_player_seq_num)
    {
        $this->friend_player_seq_num = $friend_player_seq_num;
    }

    /**
     * フレンドプレイヤーシーケンスNUM取得
     * @return int
     */
    public function getFriendPlayerSeqNum()
    {
        return $this->friend_player_seq_num;
    }

}