<?php

namespace Beans\Db;

Class ItemBean extends BaseDbBean
{

    use Traits\BeanParts\ItemId;
    use Traits\BeanParts\ItemCount;

    public function getItemType()
    {
        return ItemMater::findByPk($this->getItemId(), 'item_type');
    }

    public function getParam1()
    {
        return ItemMater::findByPk($this->getItemId(), 'param1');
    }

}