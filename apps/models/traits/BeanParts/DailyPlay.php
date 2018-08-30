<?php

namespace Traits\BeanParts;

Trait DailyPlay
{

    /**
     * 総実行回数
     * @var int
     */
    protected $daily_play = null;

    /**
     * 日別実行回数セット
     * @param int $daily_play
     */
    public function setDailyPlay(int $daily_play)
    {
        $this->daily_play = $daily_play;
    }

    /**
     * 日別実行回数取得
     * @return int
     */
    public function getDailyPlay()
    {
        if (parent::inDailyUpdateCycle(\AppConst::CYCLE_TIME_LOGIN) === true) {
            return $this->daily_play;
        } else {
            return 0;
        }
    }

}