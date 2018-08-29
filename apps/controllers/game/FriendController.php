<?php

namespace Controllers\Game;

use PlayerObject\PlayerObject;
use PlayerObject\Friend;
use PlayerObject\FriendRequest;

class FriendController extends BaseController
{

    /**
     * フレンドリスト取得
     */
    public function listAction()
    {
        AppLogger::startFunc(__METHOD__);
        AppRegistry::setDbType(AppConst::DB_TYPE_READ);

        $last_access_time = parent::getRequest('last_access_time', $required_flg = false);
        $LastAccessTime   = strlen($last_access_time) === 0 ? null : new DateTime($last_access_time);

        $this->setResponseData([
            'friend_list'         => FriendLogic::getFriendListForClient($this->player_seq_num, $LastAccessTime),
            'friend_request_list' => FriendLogic::getFriendRequestListForClient($this->player_seq_num, $LastAccessTime),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

    /**
     * フレンド申請
     */
    public function requestAction()
    {
        AppLogger::startFunc(__METHOD__);
        $request_player_open_id = parent::getRequest('request_player_open_id', $required_flg = true);

        /* @var $RequestResult \GameObject\Result\Friend\RequestResult */
        $RequestResult = FriendLogic::request($this->player_seq_num, $request_player_open_id);

        $this->setResponseData([
            'request_result'      => $RequestResult->getResultDataForClient(),
            'friend_request_list' => FriendLogic::getFriendRequestListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

    /**
     * フレンド申請「承認」
     */
    public function acceptAction()
    {
        AppLogger::startFunc(__METHOD__);
        $target_player_open_id = parent::getRequest('request_player_open_id', $required_flg = true);

        /* @var $AcceptResult \GameObject\Result\Friend\AcceptResult */
        $AcceptResult = FriendLogic::accept($this->player_seq_num, $target_player_open_id);

        $this->setResponseData([
            'result_data'         => $AcceptResult->getResultDataForClient(),
            'friend_list'         => FriendLogic::getFriendListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
            'friend_request_list' => FriendLogic::getFriendRequestListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

    /**
     * フレンド申請「却下」
     */
    public function rejectAction()
    {
        AppLogger::startFunc(__METHOD__);
        $target_player_open_id = parent::getRequest('request_player_open_id', $required_flg = true);

        /* @var $RejectResult \GameObject\Result\Friend\RejectResult */
        $RejectResult = FriendLogic::reject($this->player_seq_num, $target_player_open_id);

        $this->setResponseData([
            'result_data'         => $RejectResult->getRresultForClient(),
            'friend_request_list' => FriendLogic::getFriendRequestListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

	/**
     * フレンド申請「キャンセル」
     */
    public function cancelAction()
    {
        AppLogger::startFunc(__METHOD__);
        $target_player_open_id = $this->request->get('request_player_open_id');

        /* @var $CancelResult \GameObject\Result\Friend\CancelResult */
        $CancelResult = FriendLogic::cancel($this->player_seq_num, $target_player_open_id);

        $this->setResponseData([
            'result_data'         => $CancelResult->getRresultForClient(),
            'friend_request_list' => FriendLogic::getFriendRequestListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

	/**
     * フレンド削除
     */
    public function removeAction()
    {
        AppLogger::startFunc(__METHOD__);
        $friend_player_open_id = $this->request->get('friend_player_open_id');

        /* @var $RemoveResult \GameObject\Result\Friend\RemoveResult */
        $RemoveResult = FriendLogic::remove($this->player_seq_num, $friend_player_open_id);

        $this->setResponseData([
            'result_data' => $RemoveResult->getRresultForClient(),
            'friend_list' => FriendLogic::getFriendListForClient($this->player_seq_num, AppRegistry::getAccessTime()),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

}
