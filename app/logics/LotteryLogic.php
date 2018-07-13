<?php

use GameObject\Result\Lottery\DrawResult;
use GameObject\LotteryBox;

Class LotteryLogic
{

    /**
     * 指定抽選IDの抽選処理を行う
     * @param int $lottery_id
     * @return DrawResult
     */
    public static function draw(int $lottery_id)
    {
        AppLogger::startFunc(__METHOD__, ['$lottery_id' => $lottery_id]);
        $DrawResult = new DrawResult($lottery_id);

        $object_type = self::drawObject($lottery_id);
        switch ($object_type) {
            case AppConst::OBJECT_TYPE_CHARA: // ▼ キャラ抽選
                $chara_id = self::drawChara($lottery_id);
                $DrawResult->setDrawedCharaId($chara_id);
                break;
            case AppConst::OBJECT_TYPE_ITEM:  // ▼ アイテム抽選
                $item_data = self::drawItem($lottery_id);
                $DrawResult->setDrawedItemId($item_data['item_id']);
                $DrawResult->setDrawedItemCount($item_data['item_count']);
                break;
            default:
                throw new \Exception();
        }
        AppLogger::endFunc(__METHOD__);
        return $DrawResult;
    }

    /**
     * オブジェクト抽選
     * @param int $lottery_id
     * @return int
     */
    private static function drawObject(int $lottery_id)
    {
        $LotteryBox = new LotteryBox();
        $LotteryBox->addPrize(AppConst::OBJECT_TYPE_CHARA, 1);
        //$LotteryBox->addPrize(AppConst::OBJECT_TYPE_ITEM, 1);
        return $LotteryBox->draw();

// TODO:
        $LotteryBox  = LotteryObjectMaster::getLotteryBox($lottery_id);
        $object_type = $LotteryBox->draw();
        return $object_type;
    }

    /**
     * キャラ抽選
     * @param int $lottery_id
     * @return int
     */
    private static function drawChara(int $lottery_id)
    {
        $LotteryBox = new LotteryBox();
        $LotteryBox->addPrize(100000001, 10);
        $LotteryBox->addPrize(100000002, 20);
        $LotteryBox->addPrize(100000003, 30);
        $LotteryBox->addPrize(100000004, 20);
        $LotteryBox->addPrize(100000005, 10);
        $LotteryBox->addPrize(100000006, 10);
        return $LotteryBox->draw();

// TODO:
        $chara_rarity = self::drawCharaRarity($lottery_id);
        $LotteryBox   = LotteryCharaMaster::getLotteryBox($lottery_id, $chara_rarity);
        $chara_id     = $LotteryBox->draw();
        return $chara_id;
    }

    /**
     * アイテム抽選
     * @param int $lottery_id
     * @return array
     */
    private static function drawItem(int $lottery_id)
    {
// TODO:
        $LotteryBox = LotteryItemMaster::getLotteryBox($lottery_id);
        $item_data  = $LotteryBox->draw();
        return $item_data;
    }

    /**
     * キャラレアリティ抽選
     * @param int $lottery_id
     * @return int
     */
    private static function drawCharaRarity(int $lottery_id)
    {
// TODO:
        $LotteryBox   = LotteryCharaRarityMaster::getLotteryBox($lottery_id);
        $chara_rarity = $LotteryBox->draw();
        return $chara_rarity;
    }

}