<?php

namespace Traits\BeanParts;

Trait TotalPlayLimit
{

    /**
     * 総実行回数制限
     * @var int
     */
    protected $total_play_limit = null;

    /**
     * 総実行回数制限セット
     * @param int $total_play_limit
     */
    public function setTotalPlayLimit(int $total_play_limit)
    {
        $this->total_play_limit = $total_play_limit;
    }

    /**
     * 総実行回数制限取得
     * @return int
     */
    public function getTotalPlayLimit()
    {
        return $this->total_play_limit;
    }

}