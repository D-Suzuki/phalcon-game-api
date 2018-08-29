<?php

namespace Traits\BeanParts;

Trait ItemCount
{

    /**
     * アイテム数
     * @var int
     */
    protected $item_count = null;

    /**
     * アイテム数セット
     * @param int $item_count
     */
    public function setItemCount(int $item_count)
    {
        $this->item_count = $item_count;
    }

    /**
     * アイテム数取得
     * @return int
     */
    public function getItemCount()
    {
        return $this->item_count;
    }

}