<?php

namespace PlayerObject;

use GameObject\Reward as Reward;

Class RewardBox extends PlayerObject
{
    /**
     * 配送前報酬リスト
     * @var array
     */
    private $delivery_reward_list = [];

    /**
     * 配送済報酬リスト
     * @var array
     */
    private $delivered_reward_list = [];

    /**
     * 配送前報酬追加
     */
    public function stackReward(Reward $Reward)
    {
        $this->delivery_reward_list[] = $Reward;
    }

    /**
     * 配送前報酬追加（複数）
     */
    public function stackRewards(array $rewrad_list)
    {
        foreach($rewrad_list as $Reward) {
            $this->stackReward($Reward);
        }
    }
    
    public function addReward(Reward $Reward)
    {
        
    }

    public function syncdb()
    {
        
    }

    /**
     * 報酬配送
     * @throws Exception
     */
    public function deliver(array $reward_list = [])
    {
        if (count($reward_list) > 0) {
            $this->stackRewards($reward_list);
        }
        if (empty($this->delivery_reward_list) === true) {
            throw new Exception('配送前報酬がありません。');
        }

        foreach ($this->delivery_reward_list as $Reward) {
            $insert_list[] = [
                'reward_seq_num' => 0,
                'player_seq_num' => $this->player_seq_num,
                'reward_type'    => $Reward->getRewardType(),
                'reward_id'      => $Reward->getRewardId(),
                'reward_count'   => $Reward->getRewardCount(),
                'gift_message'   => $Reward->getGiftMessage(),
            ];
            $this->delivered_reward_list[] = $Reward; // 配送済プロパティに追加
        }
        // DB処理
        $RewardBoxTbl = \Db\Factory::getInstance(RewardBoxTbl::class, $this->player_seq_num);
        $RewardBoxTbl->bulkInsert($insert_list);

        $this->delivery_reward_list = []; // 配送前プロパティ初期化
    }

    /**
     *
     */
    public function addReward()
    {

    }

    /**
     * 受取済みにする
     */
    public function toReceived($reward_seq_num)
    {
        $RewardBoxBean = $this->getRewardBoxBean();
        if (is_null($GiftBean) === true) {
            throw new \Exception();
        }
        // Bean更新
        $RewardBoxBean->setStatus(self::STATUS_RECVED);
        $RewardBoxBean->setUpdateFlg(true);
        // DB処理
        $RewardBoxTbl = \Db\Factory::getInstance(RewardBoxTbl::class, $this->player_seq_num);
        $RewardBoxTbl->insertOrUpdate([$RewardBoxBean->toRecord()]);
    }

    /**
     * 受取
     */
    public function recv(int $reward_seq_num)
    {
        $RewardBoxBean = $this->getRewardBoxBean($reward_seq_num);
        if (is_null($RewardBoxBean) === true) {
            throw new \Exception();
        }

        switch($RewardBoxBean->getRewradType()) {
            case Rewrad::REWARD_TYPE_JEWEL:
            case Rewrad::REWARD_TYPE_CHARA:
            case Rewrad::REWARD_TYPE_ITEM:
            case Rewrad::REWARD_TYPE_COIN:
        }

        // Bean更新
        $RewardBoxBean->setStatus(self::STATUS_RECVED);
        $RewardBoxBean->setUpdateFlg(true);
        // DB処理
        $RewardBoxTbl = \Db\Factory::getInstance(RewardBoxTbl::class, $this->player_seq_num);
        $RewardBoxTbl->insertOrUpdate([$RewardBoxBean->toRecord()]);
    }

}