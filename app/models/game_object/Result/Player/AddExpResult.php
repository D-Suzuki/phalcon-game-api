<?php

namespace \GameLogic\Result\Player;

Class LoginResult extends \GameLogic\Result\BaseResult
{

    const COMPLETE = 0;

    private $player_seq_num = null;

    private $add_exp = null;

    private $scene_id = null;

    private $bef_level = null;

    private $aft_level = null;

    public function __construct(int $player_seq_num, int $add_exp, int $scene_id)
    {
        $this->player_seq_num = $player_seq_num;
        $this->add_exp        = $add_exp;
        $this->scene_id       = $scene_id;
    }

    public function setBefLevel(int $bef_level)
    {
        $this->bef_level = $bef_level;
    }

    public function setAftLevel(int $aft_level)
    {
        $this->aft_level = $aft_level;
    }

    public function createHistory()
    {

    }

}