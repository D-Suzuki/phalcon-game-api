<?php

namespace Beans\Master;

Abstract Class GachaMenuBean extends BaseMasterBean
{

    use \Traits\BeanParts\GachaId;
    use \Traits\BeanParts\Price;
    use \Traits\BeanParts\DrawCount;
    use \Traits\BeanParts\LotteryId;
    use \Traits\BeanParts\FixedDrawCount;
    use \Traits\BeanParts\FixedLotteryId;
    use \Traits\BeanParts\BonusDrawCount;
    use \Traits\BeanParts\BonusLotteryId;
    use \Traits\BeanParts\TotalPlayLimit;
    use \Traits\BeanParts\DailyPlayLimit;

    /**
     * ガチャメニューID取得
     * ※ ガチャメニュー識別用
     */
    abstract function getGachaMenuId();

    /**
     * 通常抽選回数取得
     * @return int
     */
    public function getRegularDrawCount()
    {
        return $this->getDrawCount() - $this->getFixedDrawCount();
    }

    /**
     * 確定抽選存在判定
     * @return bool
     */
    public function hasFixed()
    {
        if ($this->getFixedDrawCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * おまけ抽選存在判定
     * @return bool
     */
    public function hasBonus()
    {
        if ($this->getBonusDrawCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

}