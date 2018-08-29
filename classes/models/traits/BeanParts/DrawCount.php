<?php

namespace Traits\BeanParts;

Trait DrawCount
{

    /**
     * 抽選数
     * @var int
     */
    protected $draw_count = null;

    /**
     * 抽選数セット
     * @param int $draw_count
     */
    public function setDrawCount(int $draw_count)
    {
        $this->draw_count = $draw_count;
    }

    /**
     * 抽選数取得
     * @return int
     */
    public function getDrawCount()
    {
        return $this->draw_count;
    }

}