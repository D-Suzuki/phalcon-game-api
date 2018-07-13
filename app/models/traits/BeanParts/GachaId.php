<?php

namespace Traits\BeanParts;

Trait GachaId
{

    /**
     * ガチャID
     * @var int
     */
    protected $gacha_id = null;

    /**
     * ガチャIDセット
     * @param int $gacha_id
     */
    public function setGachaId(int $gacha_id)
    {
        $this->gacha_id = $gacha_id;
    }

    /**
     * ガチャID取得
     * @return int
     */
    public function getGachaId()
    {
        return $this->gacha_id;
    }

}