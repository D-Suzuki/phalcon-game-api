<?php

namespace GameObject;

Class Reward
{

    private $reward_type  = null;
    private $reward_id    = null;
    private $reward_count = null;
    private $reward_notes = null;

    public function __construct(int $reward_type, int $reward_id, int $reward_count, string $reward_notes = '')
    {
        $this->reward_type  = $reward_type;
        $this->reward_id    = $reward_id;
        $this->reward_count = $reward_count;
        $this->reward_notes = $reward_notes;
    }

}