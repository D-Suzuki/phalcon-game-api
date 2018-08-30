<?php

namespace Beans\Db;

Class GachaCounterBean extends BaseDbBean
{

    protected static function getColumnList()
    {
        return \Db\PlayerDb\GachaCounterTbl::$column_list;
    }

    use \Traits\BeanParts\PlayerSeqNum;
    use \Traits\BeanParts\GachaId;
    use \Traits\BeanParts\GachaMenuId;
    use \Traits\BeanParts\TotalPlay;
    use \Traits\BeanParts\DailyPlay;

}