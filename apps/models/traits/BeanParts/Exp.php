<?php

namespace Traits\BeanParts;

Trait Exp
{

    /**
     * 経験値
     * @var string
     */
    protected $exp = 0;

    /**
     * 経験値セット
     * @param int $exp
     */
    public function setExp(int $exp)
    {
        $this->exp = $exp;
    }

    /**
     * 経験値取得
     * @return int
     */
    public function getExp()
    {
        return $this->exp;
    }

}