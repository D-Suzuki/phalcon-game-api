<?php

namespace Traits\BeanParts;

Trait PlayerSeqNum
{

    /**
     * プレイヤーシーケンスNUM
     * @var int
     */
    protected $player_seq_num = null;

    /**
     * プレイヤーシーケンスNUMセット
     * @param int $player_seq_num
     */
    public function setPlayserSeqNum(int $player_seq_num)
    {
        $this->player_seq_num = $player_seq_num;
    }

    /**
     * プレイヤーシーケンスNUM取得
     * @return int
     */
    public function getPlayerSeqNum()
    {
        return $this->player_seq_num;
    }

}