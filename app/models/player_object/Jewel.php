<?php

namespace PlayerObject;

use \Db\PlayerDb\JewelTbl;
use \Beans\Db\JewelBean;

Class Jewel extends PlayerObject
{

    /**
     * ジュエル単価（無料）
     * @var int
     */
    const UNIT_PRICE_FREE = 0;

    /**
     * 使用無料分ジュエル数
     * @var int
     */
    private $used_free_jewel = 0;

    /**
     * 使用有料分ジュエル数
     * @var int
     */
    private $used_charge_jewel = 0;

    /**
     * ジュエルBeanリスト
     * @array
     */
    private $jewel_bean_list = null;

    /**
     * 使用無料分ジュエル取得
     * @return int
     */
    public function getUsedFreeJewel()
    {
        return $this->used_free_jewel;
    }

    /**
     * 使用有料分ジュエル取得
     */
    public function getUsedChargeJewel()
    {
        return $this->used_charge_jewel;
    }

    /**
     * ジュエルBeanリスト取得
     * @return array
     */
    public function getJewelBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->jewel_bean_list) === true) {
            $this->setJewelBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->jewel_bean_list;
    }

    /**
     * ジュエルBeanリスト取得
     * @param int $unit_price
     * @return array
     */
    public function getJewelBean(int $unit_price)
    {
        \AppLogger::startFunc(__METHOD__, ['$unit_price' => $unit_price]);
        $JewelBean       = null;
        $jewel_bean_list = $this->getJewelBeanList();
        if (count($jewel_bean_list) > 0 && array_key_exsits($unit_price, $jewel_bean_list) === true) {
            $JewelBean = $jewel_bean_list[$unit_price];
        }
        \AppLogger::endFunc(__METHOD__);
        return $JewelBean;
    }

    /**
     * ジュエル保持判定
     * @param int $check_count
	 * @return bool
     */
    public function hasJewel(int $check_count)
    {
        \AppLogger::startFunc(__METHOD__, ['$check_count' => $check_count]);
        $has_jewel = false;
        if ($check_count <= $this->getTotalJewel()) {
            $has_jewel = true;
        } else {
            $has_jewel = false;
        }
        \AppLogger::endFunc(__METHOD__);
        return $has_jewel;
    }

    /**
     * ジュエル使用
	 * @param int $use_count
     */
    public function decrJewel(int $use_count)
    {
        \AppLogger::startFunc(__METHOD__, ['$use_count' => $use_count]);
        if ($this->hasJewel($use_count) === false) {
            throw new \Exception();
        }
        // ▼ Bean更新
        $remain_use_count = $use_count;
        $jewel_bean_list  = $this->getJewelBeanList();
        foreach ($jewel_bean_list as $unit_price => $JewelBean) { // 有料から krsort($jewel_bean_list)  無料から ksort($jewel_bean_list)
            if ($remain_use_count === 0) {
                break;
            }
            if ($JewelBean->getCount() > $remain_use_count) {
                $this->addUsedJewel($JewelBean->getUnitPrice(), $remain_use_count);
                $JewelBean->setCount($JewelBean->getCount() - $remain_use_count);
                $JewelBean->setUpdateFlg(true);
                $remain_use_count = 0;
            } else {
                $this->addUsedJewel($JewelBean->getUnitPrice(), $JewelBean->getCount());
                $remain_use_count -= $JewelBean->getCount();
                $JewelBean->setCount(0);
                $JewelBean->setUpdateFlg(true);
            }
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * ジュエル追加
     * @param int $unit_price
     * @param int $add_count
     */
    public function incrJewel(int $unit_price, int $add_count)
    {
        \AppLogger::startFunc(__METHOD__, ['$unit_price' => $unit_price, '$add_count' => $add_count]);
        // Bean更新
        $JewelBean = $this->getJewelBean($unit_price);
        if (is_null($JewelBean) === true) {
            $JewelBean = new JewelBean([
                'player_seq_num' => $this->player_seq_num,
                'unit_price'     => $unit_price,
                'count'          => $add_count,
                'updated_at'     => AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
                'created_at'     => AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            ]);
            $this->jewel_bean_list[$unit_price] = $JewelBean;
        } else {
            $JewelBean->setCount($JewelBean->getCount() + $add_count);
        }
        $JewelBean->setUpdateFlg(true);
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * DB同期
     */
    public function syncdb()
    {
        \AppLogger::startFunc(__METHOD__);
        $sync_record_list = [];
        $jewel_bean_list  = $this->getJewelBeanList();
        if (count($jewel_bean_list) > 0) {
            foreach ($jewel_bean_list as $JewelBean) {
                if ($JewelBean->isUpdate() === true) {
                    $sync_record_list[] = $JewelBean->toRecord();
                }
            }
        }
        // DB更新
        if (count($sync_record_list) > 0) {
            $JewelTbl = \Db\Factory::getInstance(JewelTbl::class, $this->player_seq_num);
            $JewelTbl->insertOrUpdate($sync_record_list);
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * 合計ジュエル数取得
	 * @return int
     */
    private function getTotalJewel()
    {
        $total_jewel     = 0;
        $jewel_bean_list = $this->getJewelBeanList();
        if (count($jewel_bean_list) > 0) {
            foreach ($jewel_bean_list as $JewelBean) {
                $total_jewel += $JewelBean->getCount();
            }
        }
        return $total_jewel;
    }

    /**
     * 無料分ジュエル数取得
     * @return int
     */
    private function getFreeJewel()
    {
        \AppLogger::startFunc(__METHOD__);
        $free_jewel      = 0;
        $jewel_bean_list = $this->getJewelBeanList();
        if (count($jewel_bean_list) > 0) {
            foreach ($jewel_bean_list as $unit_price => $JewelBean) {
                if ($unit_price === self::UNIT_PRICE_FREE) {
                    $free_jewel += $JewelBean->getCount();
                }
            }
        }
        \AppLogger::endFunc(__METHOD__);
        return $free_jewel;
    }

    /**
     * 有料分ジュエル数取得
     * @return int
     */
    private function getChargeJewel()
    {
        \AppLogger::startFunc(__METHOD__);
        $charge_jewel    = 0;
        $jewel_bean_list = $this->getJewelBeanList();
        if (count($jewel_bean_list) > 0) {
            foreach ($jewel_bean_list as $unit_price => $JewelBean) {
                if ($unit_price > self::UNIT_PRICE_FREE) {
                    $charge_jewel += $JewelBean->getCount();
                }
            }
        }
        \AppLogger::endFunc(__METHOD__);
        return $charge_jewel;
    }

    /**
     * ジュエルBeanリストセット
     */
    private function setJewelBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        $JewelTbl    = \Db\Factory::getInstance(JewelTbl::class, $this->player_seq_num);
        $record_list = $JewelTbl->searchBy(['player_seq_num' => $this->player_seq_num]);
        if (count($record_list) > 0) {
            foreach ($record_list as $record) {
                $this->jewel_bean_list[$record['unit_price']] = new JewelBean($record);
            }
        } else {
            $this->jewel_bean_list = [];
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * 使用ジュエル数追加
     * @param int $unit_price
     * @param int $use_count
     */
    private function addUsedJewel(int $unit_price, int $use_count)
    {
        if ($unit_price > self::UNIT_PRICE_FREE) {
            $this->used_charge_jewel += $use_count;
        } else {
            $this->used_free_jewel += $use_count;
        }
    }

}