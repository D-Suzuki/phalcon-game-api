<?php

namespace GameObject\Result\Friend;

use Db\HistoryDb\FriendRequestHistoryTbl;
use Logger\AppLogger;

Class RequestResult extends \GameObject\Result\BaseResult
{

    /**
     * リザルトコード
     * @var int
     */
    const MY_REQUEST_IS_FULL = 1;
    const TO_FRIEND_IS_FULL  = 2;
    const ALREADY_SEND       = 3;
    const ALREADY_FRIEND     = 4;

    /**
     * 申請元プレイヤーシーケンスNUM
     */
    private $from_player_seq_num = null;

    /**
     * 申請先プレイヤーシーケンスNUM
     */
    private $to_player_seq_num = null;

    /**
     * コンストラクタ
     * @param int $from_player_seq_num
     * @param int $to_player_seq_num
     */
    public function __construct(int $from_player_seq_num, int $to_player_seq_num)
    {
        AppLogger::startFunc(__METHOD__, ['$from_player_seq_num' => $from_player_seq_num, '$to_player_seq_num' => $to_player_seq_num]);
        $this->from_player_seq_num = $from_player_seq_num;
        $this->to_player_seq_num   = $to_player_seq_num;
        AppLogger::endFunc(__METHOD__);
    }

    /**
     * クライアント用結果データ取得
     * @return array
     */
    public function getResultDataForClient()
    {
        AppLogger::startFunc(__METHOD__);
        $result_data = [
            'result_code' => parent::getResultCode(),
        ];
        AppLogger::endFunc(__METHOD__);
        return $result_data;
    }

    /**
     * 履歴作成
     */
    public function createHistory()
    {
        AppLogger::startFunc(__METHOD__);
        $FriendRequestHistoryTbl = \Db\Factory::getInstance(FriendRequestHistoryTbl::class);
        $FriendRequestHistoryTbl->insert([
            'seq_num'             => 0,
            'result_code'         => parent::getResultCode(),
            'from_player_seq_num' => $this->from_player_seq_num,
            'to_player_seq_num'   => $this->to_player_seq_num,
            'created_at'          => AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            'updated_at'          => AppRegistry::getAccessTime()->format('Y-m-d H:i:s')
        ]);
        AppLogger::endFunc(__METHOD__);
    }

}