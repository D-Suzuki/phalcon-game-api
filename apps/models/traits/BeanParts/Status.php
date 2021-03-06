<?php

namespace Traits\BeanParts;

Trait Status
{

    /**
     * ステータス
     * @var int
     */
    protected $status = null;

    /**
     * ステータスセット
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * ステータス取得
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

}