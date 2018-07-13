<?php

use PlayerObject\PlayerObject;
use PlayerObject\Chara;

Class ItemLogic
{

    /**
     * クライアント用アイテムリスト取得
	 * @param DataTime $LastAccessTime
	 * @param int $chara_seq_num
	 * @return array
     */
    public static function getItemListForClient(int $player_seq_num, DateTime $LastAccessTime = null, int $item_id = null) : array
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$last_access_time' => $LastAccessTime, '$chara_seq_num' => $chara_seq_num]);

        /* @var $Item \PlayerObject\Item */
        $Item = PlayerObject::getInstance($player_seq_num, Item::class);

        $item_list_for_client = [];
        $item_bean_list = $Item->getItemBeanList();
        if (is_null($item_bean_list) === false) {
            foreach ($item_bean_list as $ItemBean) {
                // 差分取得
                if (is_null($LastAccessTime) !== true && $ItemBean->getUpdateTime() < $LastAccessTime) {
                    continue;
                }
                // アイテム指定
                if (is_null($item_id) !== true && $ItemBean->getItemId() != $item_id) {
                    continue;
                }
                $item_list_for_client[] = [
                    'item_id'    => (int) $ItemBean->getItemId(),
                    'item_count' => (int) $ItemBean->getItemCount(),
                ];
            }
        }

        \AppLogger::endFunc(__METHOD__);
        return $item_list_for_client;
    }

    /**
     * アイテム追加
     * @param int $player_seq_num
     * @param array $item_list
     * @param int $scene_id
     */
    public static function addItems(int $player_seq_num, array $item_list, int $scene_id) : AddCharaResult
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$item_list' => $item_list, '$scene_id' => $scene_id]);

        $AddItemsResult = new AddItemsResult($player_seq_num, $item_list, $scene_id);

        /* @var $Item \PlayerObject\Item */
        $Item = PlayerObject::getInstance($player_seq_num, Item::class);

        // 直接追加
        foreach ($item_list as $item) {
            $Item->addItem($item['item_id'], $item['item_count']);
        }
        $Item->syncdb();

        $AddItemsResult->setResultCode(AddItemsResult::COMPLETE);
        $AddItemsResult->createHistory();
        \AppLogger::endFunc(__METHOD__);
        return $AddItemsResult;
    }

}