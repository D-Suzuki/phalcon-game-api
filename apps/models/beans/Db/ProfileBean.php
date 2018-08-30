<?php

namespace Beans\Db;

Class ProfileBean extends BaseDbBean
{

    protected static function getColumnList()
    {
        return \Db\PlayerDb\ProfileTbl::$column_list;
    }

    use \Traits\BeanParts\PlayerSeqNum;
    use \Traits\BeanParts\OpenId;
    use \Traits\BeanParts\Nickname;

}