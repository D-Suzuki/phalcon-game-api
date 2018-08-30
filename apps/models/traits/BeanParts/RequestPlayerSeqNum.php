<?php

namespace Traits\BeanParts;

Trait RequestPlayerSeqNum
{

    /**
     * 申請プレイヤーシーケンスNUM
     * @var int
     */
    protected $request_player_seq_num = null;

    /**
     * 申請プレイヤーシーケンスNUMセット
     * @param int $request_player_seq_num
     */
    public function setRequestPlayserSeqNum(int $request_player_seq_num)
    {
        $this->request_player_seq_num = $request_player_seq_num;
    }

    /**
     * 申請プレイヤーシーケンスNUM取得
     * @return int
     */
    public function getRequestPlayerSeqNum()
    {
        return $this->request_player_seq_num;
    }

}