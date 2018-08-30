<?php

namespace Traits\BeanParts;

Trait LotteryId
{

    /**
     * 抽選ID
     * @var int
     */
    protected $lottery_id = null;

    /**
     * 抽選IDセット
     * @param int $lottery_id
     */
    public function setLotteryId(int $lottery_id)
    {
        $this->lottery_id = $lottery_id;
    }

    /**
     * 抽選ID取得
     * @return int
     */
    public function getLotteryId()
    {
        return $this->lottery_id;
    }

}