<?php

namespace Traits\BeanParts;

Trait EventId
{

    /**
     * イベントID
     * @var int
     */
    protected $event_id = null;

    /**
     * イベントIDセット
     * @param int $event_id
     */
    public function setEventId(int $event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * イベントID取得
     * @return int
     */
    public function getEventId()
    {
        return $this->event_id;
    }

}