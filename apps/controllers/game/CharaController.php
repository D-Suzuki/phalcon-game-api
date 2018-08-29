<?php

namespace Controllers\Game;

use PlayerObject\PlayerObject;
use PlayerObject\Chara;

class CharaController extends BaseController
{

    /**
     * キャラリスト取得
     */
    public function listAction()
    {
        AppLogger::startFunc(__METHOD__);

        $last_access_time = $this->request->get('last_access_time');
        $LastAccessTime   = strlen($last_access_time) === 0 ? null : new DateTime($last_access_time);

        AppRegistry::setDbType(AppConst::DB_TYPE_READ);

        $this->setResponseData([
            'chara_list' => CharaLogic::getCharaListForClient($this->player_seq_num, $LastAccessTime, null),
        ]);

        AppLogger::endFunc(__METHOD__);
    }

}
