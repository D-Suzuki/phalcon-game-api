<?php

namespace \PlayerObject;

use Beans\Db\ItemBean as ItemBean;

Class Item extends PlayerObject
{

    /**
     * アイテムBeanリスト
	 * @var mixed
     */
    private $item_bean_list = null;

    /**
     * アイテム追加
	 * @param array $add_item_list
     */
    public function bulkAddItem(array $add_item_list)
    {
        $item_list = [];
        $ItemTbl   = TblFactory::getInstance(ItemTbl::class, $this->player_seq_num);
        $ItemTbl->insertOrUpdate($item_list);

        $history_list = [];
        ItemHistoryTbl::bulkInsert($history_list);

        $this->item_bean_list = null;
    }

    /**
     * アイテムBeanリスト取得
	 * @return array
     */
    public function getItemBeanList()
    {
        if (is_null($this->item_bean_list) === true) {
            $this->setItemBeanList();
        }
        return $this->item_bean_list;
    }

    /**
     * 指定アイテムBean取得
	 * @return array
     */
    public function getItemBean(int $item_id)
    {
        $ItemBean       = null;
        $item_bean_list = $this->getItemBeanList();
        if (is_null($item_bean_list) === false && array_key_exists($item_id, $item_bean_list) === true) {
            $ItemBean = $item_bean_list[$item_id];
        }
        return $ItemBean;
    }

    /**
     * アイテム追加
     * @param int $item_id
     * @param int $item_count
     */
    public function incrItem(int $item_id, int $incr_count)
    {

        $ItemBean = $this->getItemBean($item_id);
        if(is_null($ItemBean) === true) {
            $ItemBean->setItemCount($ItemBean->getItemCount() + $incr_count);
        } else {
            $ItemBean = new ItemBean([
                'item_id'    => $item_id,
                'item_count' => $incr_count,
            ]);
            $this->item_bean_list[$item_id] = $ItemBean;
        }

    }
    
    public function syncdb()
    {
        
    }

    /**
     * アイテムBeanリストセット
	 * @var mixed
     */
    private function setItemBeanList()
    {
        $ItemTbl     = TblFactory::getInstance(ItemTbl::class, $this->player_seq_num);
        $record_list = $ItemTbl->getListByPrayerSeqNum($this->player_seq_num);
        if (is_null($record_list) === true) {
            return;
        }
        foreach ($record_list as $record) {
            $this->item_bean_list[$record['item_id']] = new ItemBean($record);
        }
    }

}