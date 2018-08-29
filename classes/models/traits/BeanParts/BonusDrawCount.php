<?php

namespace Traits\BeanParts;

Trait BonusDrawCount
{

    /**
     * おまけ抽選数
     * @var int
     */
    protected $bonus_draw_count = null;

    /**
     * おまけ抽選数セット
     * @param int $bonus_draw_count
     */
    public function setBonusDrawCount(int $bonus_draw_count)
    {
        $this->bonus_draw_count = $bonus_draw_count;
    }

    /**
     * おまけ抽選数取得
     * @return int
     */
    public function getBonusDrawCount()
    {
        return $this->bonus_draw_count;
    }

}