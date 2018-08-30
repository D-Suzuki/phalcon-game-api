<?php

namespace Traits\BeanParts;

Trait CharaId
{

    /**
     * キャラID
     * @var int
     */
    protected $chara_id = null;

    /**
     * キャラIDセット
     * @param int $chara_id
     */
    public function setCharaId(int $chara_id)
    {
        $this->chara_id = $chara_id;
    }

    /**
     * キャラID取得
     * @return int
     */
    public function getCharaId()
    {
        return $this->chara_id;
    }

}