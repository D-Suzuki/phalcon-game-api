<?php

namespace Traits\BeanParts;

Trait DailyPlayLimit
{

    /**
     * 日別実行回数制限
     * @var int
     */
    protected $daily_play_limit = null;

    /**
     * 日別実行回数制限セット
     * @param int $daily_play_limit
     */
    public function setDailyPlayLimit(int $daily_play_limit)
    {
        $this->daily_play_limit = $daily_play_limit;
    }

    /**
     * 日別実行回数制限取得
     * @return int
     */
    public function getDailyPlayLimit()
    {
        return $this->daily_play_limit;
    }

}