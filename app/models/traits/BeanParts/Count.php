<?php

namespace Traits\BeanParts;

Trait Count
{

    /**
     * 数
     * @var int
     */
    protected $count = 0;

    /**
     * 数セット
     * @param int $count
     */
    public function setCount(int $count)
    {
        $this->count = $count;
    }

    /**
     * 数取得
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

}