<?php

namespace Traits\BeanParts;

Trait StepCount
{

    /**
     * ステップ数
     * @var int
     */
    protected $step_count = 0;

    /**
     * ステップ数セット
     * @param int $step_count
     */
    public function setStepCount(int $step_count)
    {
        $this->step_count = $step_count;
    }

    /**
     * ステップ数取得
     * @return int
     */
    public function getStepCount()
    {
        return $this->step_count;
    }

}