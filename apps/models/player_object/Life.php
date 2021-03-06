<?php

namespace PlayerObject;

use \Beans\Db\LifeBean;
use \Db\PlayerDb\LifeTbl;

Class Life extends PlayerObject
{

    /**
     * ライフBean
	 * @var mixed
     */
    private $LifeBean = null;

    /**
     * ライフ追加
     * @param int $incr_count
     */
    public function incrLife(int $incr_count)
    {
        AppLogger::startFunc(__METHOD__, ['$incr_count' => $incr_count]);
        $LifeBean = $this->getLiveBean();
        $LifeBean->setLastUpdateLife($LifeBean->getCurrentLife() + $incr_count);
        $LifeBean->setUpdateFlg(true);
        parent::setSyncableFlg(true);
        AppLogger::endFunc(__METHOD__);
	}

    /**
     * ライフ消費
     * @param int $decr_count
     */
    public function decrLife(int $decr_count)
    {
        AppLogger::startFunc(__METHOD__, ['$decr_count' => $decr_count]);
        if ($this->hasLife($decr_count) === false) {
            throw new Exception('has not life');
        }
        $LifeBean = $this->getLiveBean();
        $LifeBean->setLastUpdateLife($LifeBean->getCurrentLife() - $decr_count);
        $LifeBean->setUpdateFlg(true);
        parent::setSyncableFlg(true);
        AppLogger::endFunc(__METHOD__);
	}

    /**
     * DB同期
     */
	public function syncdb()
	{
	    AppLogger::startFunc(__METHOD__);
	    if (parent::isSyncable() === false) {
	        return false;
	    }
	    $LifeBean = $this->getLifeBean();
        $LifeTbl  = \Db\Factory::getInstance(LifeTbl::class, $this->player_seq_num);
        $LifeTbl->insertOrUpdate([$LifeBean->toRecord()]);
	    parent::setSyncableFlg(false);
	    AppLogger::endFunc(__METHOD__);
	}

    /**
     * ライフ保持判定
     * @param int $has_count
	 * @return bool
     */
    public function hasLife(int $has_count)
    {
        AppLogger::startFunc(__METHOD__);
        $has_life = false;
        $LifeBean = $this->getLifeBean();
        if ($has_count <= $LifeBean->getCurrentLife()) {
            $has_life = true;
        }
        AppLogger::endFunc(__METHOD__);
        return $has_life;
	}

    /**
     * ライフBean取得
	 * @return array
     */
    public function getLifeBean()
    {
        AppLogger::startFunc(__METHOD__);
        if (is_null($this->LifeBean) === true) {
            $this->setLifeBean();
        }
        AppLogger::endFunc(__METHOD__);
        return $this->LifeBean;
    }

    /**
     * ライフBeanセット
	 * @var mixed
     */
    private function setLifeBean()
    {
        AppLogger::startFunc(__METHOD__);
        $LifeTbl     = \Db\Factory::getInstance(LifeTbl::class, $this->player_seq_num);
        $record_list = $LifeTbl->findByPk($this->player_seq_num);
        if (is_null($record_list) === true) {
            return;
        }
        $this->LifeBean = new LifeBean($record_list[0]);
        AppLogger::endFunc(__METHOD__);
    }

}