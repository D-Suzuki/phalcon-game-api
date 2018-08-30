<?php

namespace Traits\BeanParts;

Trait UnitPrice
{

    /**
     * 単価
     * @var int
     */
    protected $unit_price = null;

    /**
     * 単価セット
     * @param int $status
     */
    public function setUnitPrice(int $unit_price)
    {
        $this->unit_price = $unit_price;
    }

    /**
     * 単価取得
     * @return int
     */
    public function getUnitPrice()
    {
        return $this->unit_price;
    }

}