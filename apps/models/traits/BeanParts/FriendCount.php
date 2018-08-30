<?php

namespace Traits\BeanParts;

Trait FriendCount
{

    /**
     * フレンド数
     * @var int
     */
    protected $friend_count = 0;

    /**
     * フレンド数セット
     * @param int $friend_count
     */
    public function setFriendCount(int $friend_count)
    {
        $this->friend_count = $friend_count;
    }

    /**
     * フレンド数取得
     * @return int
     */
    public function getFriendCount()
    {
        return $this->friend_count;
    }

}