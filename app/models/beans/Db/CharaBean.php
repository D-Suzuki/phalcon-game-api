<?php

namespace Beans\Db;

use GameObject\Master\CharaLevelMaster;

Class CharaBean extends BaseDbBean
{

    /**
     * コンストラクタ
     * @param array $record
     */
    public function __construct(array $record)
    {
        parent::__construct($record);
        self::$level_master_class = CharaLevelMaster::class;
    }

    use \Traits\BeanParts\CharaSeqNum;
    use \Traits\BeanParts\PlayerSeqNum;
    use \Traits\BeanParts\CharaId;
    use \Traits\BeanParts\Status;

    use \Traits\BeanLogics\ExpToLevel;

     /**
     * カラムリスト取得
     * @return array
     */
    protected static function getColumnList()
    {
        return \Db\PlayerDb\CharaTbl::$column_list;
    }

}