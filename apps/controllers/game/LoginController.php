<?php

namespace Controllers\Game;

use PlayerObject\PlayerObject;
use PlayerObject\Profile;

class ProfileController extends BaseController
{

    /**
     * ニックネーム変更
     */
    public function renameAction()
    {
        \AppLogger::startFunct(__METHOD__);
        $nickname = $this->request->get('nickname');

        /* @var $RenameResult \GameObject\Result\Player\RenameResult */
        $RenameResult = PlayerLogic::rename($this->player_seq_num, $nickname);

        /* @var $Profile \PlayerObject\Profile */
        $Profile = PlayerObject::getInstance($this->player_seq_num, Profile::class); /* @var $Profile \PlayerObject\Profile */

        $this->setResponseData([
            'reneme_result' => $RenameResult->getResultForClient(),
            'profile_data'  => $Profile->getProfileDataForClient(),
        ]);

        \AppLogger::endFunc(__METHOD__);
    }

}
