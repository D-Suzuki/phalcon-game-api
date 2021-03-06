<?php

namespace Beans\Db;

use GameObject\DailyCycle;

Abstract Class BaseDbBean extends \Beans\BaseBean
{

    use \Traits\BeanParts\CreatedAt;
    use \Traits\BeanParts\UpdatedAt;

    /**
     * 更新フラグ
     * @var bool
     */
    protected $update_flg = false;

    /**
     * カラムリスト取得
     * ※抽象メソッド
     * @return array
     */
    abstract protected static function getColumnList();

    /**
     * コンストラクタ
     * @param array $record
     */
    public function __construct(array $record = null)
    {
        parent::__construct($record);
    }

    /**
     * 更新フラグセット
     * @param string $property_name
     */
    public function setUpdateFlg(bool $update_flg)
    {
        $this->update_flg = $update_flg;
        if ($update_flg === true) {
            $this->setUpdatedAt(\AppRegistry::getAccessTime()->format('Y-m-d H:i:s'));
        }
	}

    /**
     * 更新判定
     * @return bool
     */
    public function isUpdate()
    {
        return $this->update_flg;
    }

    /**
     * レコード配列へ変換
     * @return array
     */
    public function toRecord()
    {
        $record = [];
        foreach ($this->getColumnList() as $priperty_name) {
            $value = parent::getProperty($priperty_name);
            //if ($value instanceof \DateTime) {
            //    $record[$priperty_name] = $value->format('Y-m-d H:i:s');
            //} else {
                $record[$priperty_name] = $value;
            //}
        }
        return $record;
    }

    /**
     * デイリー更新サイクル内判定
     * true  -> 前回更新日時から日付を超えていない
     * false -> 前回更新日時から日付を超えた
     * @param string $cycle_time "HH:MM:SS"
     * @return bool
     */
    protected function inDailyUpdateCycle(string $cycle_time)
    {
        return DailyCycle::inCycle($this->getUpdatedAt(), \AppRegistry::getAccessTime(), $cycle_time);
    }

}