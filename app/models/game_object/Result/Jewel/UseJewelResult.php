<?php

namespace GameObject\Result\Jewel;

use \Db\HistoryDb\UseJewelHistoryTbl;

Class UseJewelResult extends \GameObject\Result\BaseResult
{
    
    const IS_NOT_ENOUGH = 1;

    /**
     * プレイヤーシーケンスNUM
     * @var int
     */
    private $player_seq_num = null;

    /**
     * 使用数
     * @var int
     */
    private $use_count = null;

    /**
     * 無料分使用数
     * @var int
     */
    private $used_free_jewel = null;

    /**
     * 有料分使用数
     * @var int
     */
    private $used_charge_jewel = null;

    /**
     * シーンID
     * @var int
     */
    private $scene_id = null;

    /**
     * コンストラクタ
     * @param int $player_seq_num
     * @param int $use_count
     * @param int $scene_id
     */
    public function __construct(int $player_seq_num, int $use_count, int $scene_id)
    {
        $this->player_seq_num = $player_seq_num;
        $this->use_ocunt      = $use_count;
        $this->scene_id       = $scene_id;
    }

    /**
     * 使用無料分ジュエル数セット
     * @param int $used_free_jewel
     */
    public function setUsedFreeJewel(int $used_free_jewel)
    {
        $this->used_free_jewel = $used_free_jewel;
    }

    /**
     * 使用有料分ジュエル数セット
     * @param int $used_charge_jewel
     */
    public function setUsedChargeJewel(int $used_charge_jewel)
    {
        $this->used_charge_jewel = $used_charge_jewel;
    }

    /**
     * 履歴生成
     */
    public function createHistory()
    {
        $UseJewelHistoryTbl = \Db\Factory::getInstance(UseJewelHistoryTbl::class);
        $UseJewelHistoryTbl->insert([
            'player_seq_num'    => $this->player_seq_num,
            'used_free_jewel'   => $this->used_free_jewel,
            'used_charge_jewel' => $this->used_charge_jewel,
            'scene_id'          => $this->scene_id,
            'created_at'        => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            'updated_at'        => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
        ]);
    }

}