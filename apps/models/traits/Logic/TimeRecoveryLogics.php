<?php

namespace Traits\Logics;

trait TimeRecoveryLogics
{

    /* ▼ ------------------- ▼ private property ▼ ------------------- ▼ */

    /**
     * 回復秒
     * @var int
     */
    private $recovery_sec = null;

    /**
     * 回復単位
     * @var int
     */
    private $recovery_unit = null;

    /**
     * 現在日時
     * @var DateTime
     */
    private $current_time = null;

    /**
     * 最終更新日時
     * @var DateTime
     */
    private $last_update_time = null;

    /* ▲ ------------------- ▲ private property ▲ ------------------- ▲ */
    /* ▼ ------------------- ▼ public function ▼ -------------------- ▼ */

    /**
     * 回復値取得
     * @return int
     */
    public function getRecoveryValue()
    {
        $diff_sec = $this->getCurrentTime()->format('U') - $this->getLastRecoveryTime()->format('U');

        $recovery_count = (int) ($diff_sec / $this->recovery_sec);
        $recovery_value = $recovery_count * $this->getRecoveryUnit();

        return $recovery_value;
    }

    /* ▲ ------------------- ▲ public function ▲ -------------------- ▲ */
    /* ▼ ------------------ ▼ private function ▼ -------------------- ▼ */

    /**
     * 回復秒セット
     * @var int
     */
    private function setRecoverySec(int $recovery_sec)
    {
        $this->recovery_sec = $recovery_sec;
	}

    /**
     * 回復単位セット
     * @param DateTime $recovery_unit
     */
    private function setRecoveryUnit(int $recovery_unit)
    {
        $this->recovery_unit = $recovery_unit;
	}

    /**
     * 現在日時セット
     * @param DateTime $current_time
     */
    private function setCurrentTime(DateTime $current_time)
    {
        $this->current_time = $current_time;
	}

    /**
     * 最終更新日時セット
     * @param DateTime $last_update_time
     */
    private function setLastUpdateTime(DateTime $last_update_time)
    {
        $this->last_update_time = $last_update_time;
	}

    /* ▲  ---------------- ▲  private function ▲  ------------------- ▲ */

}
