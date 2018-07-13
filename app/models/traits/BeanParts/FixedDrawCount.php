<?php

namespace Traits\BeanParts;

Trait FixedDrawCount
{

    /**
     * 確定抽選数
     * @var int
     */
    protected $fixed_draw_count = null;

    /**
     * 抽選数セット
     * @param int $fixed_draw_count
     */
    public function setFixedDrawCount(int $fixed_draw_count)
    {
        $this->fixed_draw_count = $fixed_draw_count;
    }

    /**
     * 抽選数取得
     * @return int
     */
    public function getFixedDrawCount()
    {
        return $this->fixed_draw_count;
    }

}