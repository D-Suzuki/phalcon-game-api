<?php

namespace Traits\BeanParts;

Trait Nickname
{

    /**
     * ニックネームID
     * @var int
     */
    protected $nickname = null;

    /**
     * ニックネームセット
     * @param string $chara_id
     */
    public function setNickname(string $nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * ニックネーム取得
     * @return int
     */
    public function getNickname()
    {
        return $this->nickname;
    }

}