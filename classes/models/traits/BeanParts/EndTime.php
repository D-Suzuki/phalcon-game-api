<?php

namespace Traits\BeanParts;

Trait EndTime
{

    /**
     * 終了日時
     * @var mixed
     */
    protected $end_time = null;

    /**
     * 終了日時セット
     * @param string $end_time
     */
    public function setEndTime(string $end_time)
    {
        $this->end_time = new \DateTime($end_time);
    }

    /**
     * 終了日時取得
     * @return DateTime
     */
    public function getEndTime()
    {
        if (is_string($this->end_time) === true) {
            $this->end_time = new \DateTime($this->end_time);
        }
        return $this->end_time;
    }

}