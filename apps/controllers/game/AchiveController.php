<?php

namespace Controllers\Game;

use PlayerObject\PlayerObject;
use Logics\AchiveLogic;

class AchiveController extends BaseController
{

    /**
     * アチーブリスト取得
     * @return null
     */
    public function listAction()
    {
        \AppLogger::startFunc(__METHOD__);
        AppRegistry::setDbType(\AppConst::DB_TYPE_READ);

        $this->setResponseData([
            'achive_list' => AchiveLogic::getAchiveListForClient($this->player_seq_num),
        ]);
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * アチーブクリア
     */
    public function clearAction()
    {
        \AppLogger::startFunc(__METHOD__);
        $achive_id = $this->request->get('achive_id');

        /* @var $ClearResult \GameLogic\Result\Achive\ClearResult */
        $ClearResult = AchiveLogic::clearAchive($this->player_seq_num, $achive_id);

        $this->setResponseData([
            'clear_result' => $ClearResult->getRresultForClient(),
            'achive_list'  => AchiveLogic::getAchiveListForClient($this->player_seq_num),
        ]);
        \AppLogger::endFunc(__METHOD__);
    }

}
