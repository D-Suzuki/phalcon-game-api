<?php

namespace PlayerObject;

Abstract Class PlayerObject
{

    /**
     * プレイヤーシーケンスNUM
     * @var int
     */
    protected $player_seq_num = -1;

    /**
     * 同期可能フラグ
     * @var bool
     */
    private $syncable_flg = false;

    /**
     * コンストラクタ
     * @param int $player_seq_num
     */
    protected function __construct($player_seq_num)
    {
        $this->player_seq_num = $player_seq_num;
    }

    /**
     * DB同期メソッド
     */
    abstract public function syncdb();

    /**
     * プレイヤーシーケンスNUM取得
     * @return int
     */
    public function getPlayerSeqNum()
    {
        return $this->player_seq_num;
    }

    /**
     * 同期可能判定
     * @var bool
     */
    public function isSyncable() {
        return $this->syncable_flg;
    }

    /**
     * 同期可能フラグセット
     * @param bool
     */
    protected function setSyncableFlg(bool $syncable_flg) {
        $this->syncable_flg = $syncable_flg;
    }

    /**
     * プレイヤーオブジェクト配列
     * @var array[player_seq_num][PlayerObject]
     */
    private static $player_instance_list = [];

    /**
     * プレイヤーオブジェクト取得
     * @param int $player_seq_num
     * @param string $class
     * @return PlayerObject
     */
    public static function getInstance($player_seq_num, $class)
    {
        $stack_key = $player_seq_num . '_' . $class;
        if (array_key_exists($stack_key, self::$player_instance_list) === false) {
            self::$player_instance_list[$stack_key] = new $class($player_seq_num);
        }
        return self::$player_instance_list[$stack_key];
    }

}