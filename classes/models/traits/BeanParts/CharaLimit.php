<?php

namespace Traits\BeanParts;

Trait CharaLimit
{

    /**
     * キャラ上限
     * @var int
     */
    protected $chara_limit = 0;

    /**
     * キャラ上限セット
     * @param int $chara_limit
     */
    public function setCharaLimit(int $chara_limit)
    {
        $this->chara_limit = $chara_limit;
    }

    /**
     * キャラ上限取得
     * @return int
     */
    public function getCharaLimit()
    {
        return $this->chara_limit;
    }

}