<?php

namespace Traits\BeanParts;

Trait Price
{

    /**
     * 価格
     * @var int
     */
    protected $price = null;

    /**
     * 価格セット
     * @param int $price_type
     */
    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    /**
     * 価格取得
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

}