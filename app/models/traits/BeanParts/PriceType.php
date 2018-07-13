<?php

namespace Traits\BeanParts;

Trait PriceType
{

    /**
     * 価格タイプ
     * @var int
     */
    protected $price_type = null;

    /**
     * 価格タイプセット
     * @param int $price_type
     */
    public function setPriceType(int $price_type)
    {
        $this->price_type = $price_type;
    }

    /**
     * 価格タイプ取得
     * @return int
     */
    public function getPriceType()
    {
        return $this->price_type;
    }

}