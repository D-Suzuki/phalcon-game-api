<?php

namespace Beans\Db;

use GameObject\Master\PlayerLevelMaster;

Class PlayerBean extends BaseDbBean
{

    /**
     * コンストラクタ
     * @param array $record
     */
    public function __construct(array $record)
    {
        parent::__construct($record);
        self::$level_master_class = PlayerLevelMaster::class;
    }

    use \Traits\BeanParts\PlayerSeqNum;
    use \Traits\BeanParts\Status;
    use \Traits\BeanParts\CharaLimit;
    use \Traits\BeanParts\FriendLimit;

    use \Traits\BeanLogics\ExpToLevel;

    /**
     * カラムリスト取得
     * @return array
     */
    protected static function getColumnList()
    {
        return \Db\PlayerDb\PlayerTbl::$column_list;
    }

}