<?php

namespace Traits\BeanParts;

Trait FriendLimit
{

    /**
     * フレンド上限
     * @var int
     */
    protected $friend_limit = 0;

    /**
     * フレンド上限セット
     * @param int $chara_limit
     */
    public function setFriendLimit(int $friend_limit)
    {
        $this->friend_limit = $friend_limit;
    }

    /**
     * フレンド上限取得
     * @return int
     */
    public function getFriendLimit()
    {
        return $this->friend_limit;
    }

}