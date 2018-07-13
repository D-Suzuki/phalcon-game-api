<?php

namespace Traits\BeanParts;

Trait TotalPlay
{

    /**
     * 総実行回数
     * @var int
     */
    protected $total_play = null;

    /**
     * 総実行回数セット
     * @param int $total_play
     */
    public function setTotalPlay(int $total_play)
    {
        $this->total_play = $total_play;
    }

    /**
     * 総実行回数取得
     * @return int
     */
    public function getTotalPlay()
    {
        return $this->total_play;
    }

}