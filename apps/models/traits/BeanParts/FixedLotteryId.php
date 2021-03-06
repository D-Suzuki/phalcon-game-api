<?php

namespace Traits\BeanParts;

Trait FixedLotteryId
{

    /**
     * 確定抽選ID
     * @var int
     */
    protected $fixed_lottery_id = null;

    /**
     * 確定抽選IDセット
     * @param int $fixed_lottery_id
     */
    public function setFixedLotteryId(int $fixed_lottery_id)
    {
        $this->fixed_lottery_id = $fixed_lottery_id;
    }

    /**
     * 確定抽選ID取得
     * @return int
     */
    public function getFixedLotteryId()
    {
        return $this->fixed_lottery_id;
    }

}