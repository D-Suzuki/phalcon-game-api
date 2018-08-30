<?php

namespace Beans\Db;

Class FriendCounterBean extends BaseDbBean
{

    protected static function getColumnList()
    {
        return \Db\ShareDb\FriendCounterTbl::$column_list;
    }

    use \Traits\BeanParts\PlayerSeqNum;
    use \Traits\BeanParts\FriendCount;
    use \Traits\BeanParts\RequestCount;

}