<?php

namespace Beans\Db;

Class LifeBean extends BaseDbBean
{

    const RECOVERY_SEC = 300;

    const RECOVERY_UNIT = 1;

    /**
     * 時間回復ロジック用トレイト
     */
    use TimeRecoveryLogics;

    /**
     * 最後に更新された時のライフ数
     * @var int
     */
    protected $last_update_life = null;

    /**
     * ライフタイプ
     * @var int
     */
    protected $life_type = null;

    /**
     * 最大ライフ数
     * @var int
     */
    protected $max_life = null;

    /**
     * コンストラクタ
	 * @param array $record
     */
    public function __construct($record = null)
    {
        $this->setRecoverySec(self::RECOVERY_SEC);
        $this->setRecoveryUnit(self::RECOVERY_UNIT);
        $this->SetCurrentTime(GlobalRegistory::getAccessTime());
        $this->setLastUpdateTime(new DateTime($record['update_time']));
        parent::__construct($record);
    }

    /**
     * 最後に更新された時のライフ数セット
     * @param int $last_update_life
     */
    public function setLastUpdateLife($last_update_life)
    {
        $this->last_update_life = $last_update_life;
    }

    /**
     * 最大ライフ数セット
     * @param int $max_life
     */
    public function setMaxLife($max_life)
    {
        $this->max_life = $max_life;
    }

    /**
     * 更新日時セット
     * @param DateTime $update_time
     */
    public function setUpdateTime(DateTime $update_time)
    {
        $this->update_time = $update_time;
        $this->setLastRecoveryTime($update_time);
    }

    /**
     * 現在のライフ取得
     * @param int $update_time
     */
    public function getCurrentLife()
    {
        $recovery_life = $this->getRecoveryValue() + $this->getLastUpdateLife();
        // 最大ライフ数チェック
        if ($this->getMaxLife() < $recovery_life) {
            $current_life = $this->getMaxLife();
        } else {
            $current_life = $recovery_life;
        }

        return $current_life;
    }

}