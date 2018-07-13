<?php

namespace GameObject\Result\Lottery;

Class DrawResult extends \GameObject\Result\BaseResult
{

    /**
     * 抽選ID
     */
    private $lottery_id = null;

    /**
     * 確定抽選フラグ
     * @var bool
     */
    private $fixed_flg = false;

    /**
     * ボーナス抽選フラグ
     * @var bool
     */
    private $bonus_flg = false;

    /**
     * 抽選されたキャラID
     * @var int
     */
    private $drawed_chara_id = null;

    /**
     * 抽選されたアイテムID
     * @var int
     */
    private $drawed_item_id = null;

    /**
     * 抽選されたアイテム数
     * @var int
     */
    private $drawed_item_count = null;

    /**
     * コンストラクタ
     * @int $lottery_id
     */
    public function __construct(int $lottery_id)
    {
        $this->lottery_id = $lottery_id;
    }

    /**
     * 抽選ID取得
     * @return int
     */
    public function getLotteryId()
    {
        return $this->lottery_id;
    }

    /**
     * 確定抽選フラグセット
     * @param bool $fixed_flg
     */
    public function setFixedFlg(bool $fixed_flg)
    {
        $this->fixed_flg = $fixed_flg;
    }

    /**
     * 確定抽選判定
     * @return bool
     */
    public function isFixed()
    {
        return $this->fixed_flg;
    }

    /**
     * ボーナス抽選フラグセット
     * @param bool $bonus_flg
     */
    public function setBonusFlg(bool $bonus_flg)
    {
        $this->bonus_flg = $bonus_flg;
    }

    /**
     * ボーナス抽選判定
     * @return bool
     */
    public function isBonus()
    {
        return $this->bonus_flg;
    }

    /**
     * 抽選されたキャラIDをセット
     * @param int $chara_id
     */
    public function setDrawedCharaId(int $drawed_chara_id)
    {
        $this->drawed_chara_id = $drawed_chara_id;
    }

    /**
     * 抽選されたキャラIDを取得
     * @return int
     */
    public function getDrawedCharaId()
    {
        return $this->drawed_chara_id;
    }

    /**
     * 抽選されたアイテムIDをセット
     * @param int $drawed_item_id
     */
    public function setDrawedItemId(int $drawed_item_id)
    {
        $this->drawed_item_id = $drawed_item_id;
    }

    /**
     * 抽選されたアイテムIDを取得
     * @return array
     */
    public function getDrawedItemId()
    {
        return $this->drawed_item_id;
    }

    /**
     * 抽選されたアイテム数をセット
     * @param int $drawed_item_count
     */
    public function setDrawedItemCount(int $drawed_item_count)
    {
        $this->drawed_item_count = $drawed_item_count;
    }

    /**
     * 抽選されたアイテム数を取得
     * @return array
     */
    public function getDrawedItemCount()
    {
        return $this->drawed_item_count;
    }

}