<?php

namespace Traits\BeanParts;

Trait GachaType
{

    /**
     * ガチャタイプ
     * @var int
     */
    protected $gacha_type = null;

    /**
     * ガチャタイプセット
     * @param int $gacha_type
     */
    public function setGachaType(int $gacha_type)
    {
        $this->gacha_type = $gacha_type;
    }

    /**
     * ガチャタイプ取得
     * @return int
     */
    public function getGachaType()
    {
        return $this->gacha_type;
    }

}