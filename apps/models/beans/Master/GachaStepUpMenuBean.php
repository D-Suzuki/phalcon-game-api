<?php

namespace Beans\Master;

Class GachaStepUpMenuBean extends GachaMenuBean
{

    use \Traits\BeanParts\StepCount;

    /**
     * ガチャメニューID取得
     * @return int
     * @see parent::getGachaMenuId()
     */
    public function getGachaMenuId()
    {
        return $this->step_count;
    }

}