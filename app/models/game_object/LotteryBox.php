<?php

namespace GameObject;

/**
 * 抽選箱クラス
 */
Class LotteryBox
{

    /**
     * 賞品リスト
     * @var array
     */
    private $prize_list = [];

    /**
     * 賞品追加
     * @param mixed $prize
     * @param int $weight 抽選重み
     */
    public function addPrize($prize, int $weight)
    {
        if ($weight <= 0) {
            return;
        }
        $this->prize_list[] = [
            'prize'  => $prize,
            'weight' => $weight,
        ];
    }

    /**
     * 賞品リストリセット
     */
    public function resetBox()
    {
        $this->prize_list = [];
    }

    /**
     * くじ引き
     * @return mixed
     */
    public function draw()
    {
        \AppLogger::startFunc(__METHOD__);
        if (empty($this->prize_list) === true) {
            throw new \Exception('prize list is empty');
        }
		// 抽選箱をシャッフル
		shuffle($this->prize_list);
		// 抽選対象配列を作成
		$target_list  = [];
		$total_weight = 0;
		foreach ($this->prize_list as $prize_data) {
		    $from_weight   = $total_weight;
			$total_weight += $prize_data['weight'];
			$to_weight     = $total_weight;
			$target_list[] = [
			    'from_weight' => $from_weight,
			    'to_weight'   => $to_weight,
			    'prize'       => $prize_data['prize'],
			];
		}
		// ランダム抽選
		$randon_num = mt_rand(1, $total_weight);
		foreach ($target_list as $target) {
            if ($target['from_weight'] < $randon_num && $randon_num <= $target['to_weight']) {
                $prize = $target['prize'];
                break;
            }
		}
        \AppLogger::endFunc(__METHOD__, $prize);
		return $prize;
    }

}