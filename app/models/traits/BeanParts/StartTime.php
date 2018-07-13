<?php

namespace Traits\BeanParts;

Trait StartTime
{

    /**
     * 開始日時
     * @var mixed
     */
    protected $start_time = null;

    /**
     * 開始日時セット
     * @param string $start_time
     */
    public function setStartTime(string $start_time)
    {
        $this->start_time = $start_time;
    }

    /**
     * 開始日時取得
     * @return DateTime
     */
    public function getStartTime()
    {
        if (is_string($this->start_time) === true) {
            $this->start_time = new \DateTime($this->start_time);
        }
        return $this->start_time;
    }

}