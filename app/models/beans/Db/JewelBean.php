<?php

namespace Beans\Db;

Class JewelBean extends BaseDbBean
{

    /**
     * カラムリスト取得
     * @return array
     */
    protected static function getColumnList()
    {
        return \Db\PlayerDb\JewelTbl::$column_list;
    }

    use \Traits\BeanParts\PlayerSeqNum;
    use \Traits\BeanParts\UnitPrice;
    use \Traits\BeanParts\Count;

}