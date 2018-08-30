<?php

namespace Logics;

use GameObject\Result\Friend\RequestResult;
use GameObject\Result\Friend\AcceptResult;
use GameObject\Result\Friend\RejectResult;
use GameObject\Result\Friend\CancelResult;
use GameObject\Result\Friend\RemoveResult;
use PlayerObject\PlayerObject;
use PlayerObject\Friend;
use PlayerObject\FriendRequest;
use PlayerObject\FriendCounter;

Class FriendLogic
{

    /**
     * クライアント用フレンドリスト取得
     * @param int $from_player_seq_num
     * @param DateTime $LastAccessTime
     * @return array
     */
    public static function getFriendListForClient(int $from_player_seq_num, DateTime $LastGetTime = null)
    {
        AppLogger::startFunc(__METHOD__, ['$from_player_seq_num' => $from_player_seq_num, '$LastAccessTime' => $LastAccessTime]);

        /* @var $Friend \PlayerObject\Friend */
        $Friend = PlayerObject::getInstance($player_seq_num, Friend::class);

        $friend_list_for_client = [];
        if (count($Friend->getFriendBeanList()) > 0) {
            foreach ($Friend->getFriendBeanList() as $FriendBean) {
                // 差分取得
                if (is_null($LastAccessTime) === false && $FriendBean->getUpdatedAt() < $LastAccessTime) {
                    continue;
                }
                $friend_list_for_client[] = [
                    'friend_player_seq_num' => (int) $FriendBean->getFriendPlayerSeqNum(),
                ];
            }
        }

        AppLogger::endFunc(__METHOD__);
        return $friend_list_for_client;
    }

    /**
     * クライアント用フレンド申請リスト取得
     * @param int $from_player_seq_num
     * @param DateTime $LastAccessTime
     * @return array
     */
    public static function getFriendRequestListForClient(int $from_player_seq_num, DateTime $LastAccessTime = null)
    {
        AppLogger::startFunc(__METHOD__, ['$from_player_seq_num' => $from_player_seq_num, '$LastAccessTime' => $LastAccessTime]);

        $friend_request_list_for_client = [];

        /* @var $FriendRequest \PlayerObject\FriendRequest */
        $FriendRequest = PlayerObject::getInstance($player_seq_num, Friend::class);
        if (count($FriendRequest->getFriendRequestBeanList()) > 0) {
            foreach ($FriendRequest->getFriendRequestBeanList() as $FriendRequestBean) {
                // 差分取得
                if (is_null($LastAccessTime) === false && $FriendRequestBean->getUpdatedAt() < $LastAccessTime) {
                    continue;
                }
                $friend_request_list_for_client[] = [
                    'request_type'           => (int) $FriendRequestBean->getRequestType(),
                    'request_player_seq_num' => (int) $FriendRequestBean->getRequestPlayerSeqNum(),
                ];
            }
        }

        AppLogger::endFunc(__METHOD__);
        return $friend_request_list_for_client;
    }


    /**
     * フレンド申請
     * @param int $from_player_seq_num
     * @param int $to_player_seq_num
     * @return \GameLogic\Result\Friend\RequestResult
     */
    public static function request(int $from_player_seq_num, int $to_player_open_id)
    {
        AppLogger::startFunc(__METHOD__, ['$from_player_seq_num' => $from_player_seq_num, '$to_player_open_id' => $to_player_open_id]);

        // オープンIDから相手のプレイヤーシーケンスNUMを取得
        $to_player_seq_num = Player::getPlayerSeqNumByPlayerOpenId($to_player_open_id);

        // 必要インスタンス生成
        $RequestResult     = new RequestResult($from_player_seq_num, $to_player_seq_num);
        $FromFriendCounter = PlayerObject::getInstance($from_player_seq_num, FriendCounter::class); // 自分用
        $FromFriendRequest = PlayerObject::getInstance($from_player_seq_num, FriendRequest::class);
        $FromFriend        = PlayerObject::getInstance($from_player_seq_num, Friend::class);
        $ToFriendCounter   = PlayerObject::getInstance($to_player_seq_num, FriendCounter::class); // 相手用
        $ToFriendRequest   = PlayerObject::getInstance($to_player_seq_num, FriendRequest::class);
        $ToFriend          = PlayerObject::getInstance($to_player_seq_num, Friend::class);

        // フレンドカウンターテーブルをロック
        FriendCounter::lockFriendCounter([$from_player_seq_num, $to_player_seq_num]);

        /*                                                  ◆ 各種チェック
          ================================================================= */
        // ▼ 自分の申請送信制限チェック
        if ($FromFriendCounter->isFullRequest() === true) {
            $RequestResult->setResultCode(RequestResult::MY_REQUEST_IS_FULL);
            AppLogger::endFunc(__METHOD__);
            return $RequestResult;
        }
        // ▼ 相手のフレンド数制限チェック
        if ($ToFriendCounter->isFullFriend() === true) {
            $RequestResult->setResultCode(RequestResult::TO_FRIEND_IS_FULL);
            AppLogger::endFunc(__METHOD__);
            return $RequestResult;
        }
        // ▼ 申請送信済みチェック
        if ($FromFriendRequest->hasSendRequest($to_player_seq_num) === true) {
            $RequestResult->setResultCode(RequestResult::ALREADY_REQUEST);
            AppLogger::endFunc(__METHOD__);
            return $RequestResult;
        }
        // ▼ フレンド済みチェック
        if (is_null($FromFriend->isFriend($to_player_seq_num)) === true) {
            $RequestResult->setResultCode(RequestResult::ALREADY_FRIEND);
            AppLogger::endFunc(__METHOD__);
            return $RequestResult;
        }

        /*                                                     ◆ 申請処理
          ================================================================= */
        // 相手からの申請があればフレンド成立
        if ($FromFriendRequest->hasRecvRequest($to_player_seq_num) === true) {

            // 申請ステータス更新
            $FromFriendRequest->updateSendRequest($to_player_seq_num, FriendRequest::REQUEST_STATUS_ACCEPTED);
            $ToFriendRequest->updateRecvRequest($from_player_seq_num, FriendRequest::REQUEST_STATUS_ACCEPTED);
            // フレンド追加
            $FromFriend->addFriend($to_player_seq_num);
            $ToFriend->addFriend($from_player_seq_num);
            // フレンド数インクリメント
            $FromFriendCounter->incrFriend(true);
            $ToFriendCounter->incrFriend(false);
            // 申請数デクリメント
            $ToFriendCounter->decrRequest(true);
            // リザルトセット
            $RequestResult->setResultCode(RequestResult::BUILD_FRIENDSHIP);

        // 申請データ追加
        } else {

            // 申請追加
            $FromFriendRequest->addRequest(FriendRequest::REQUEST_TYPE_SEND, $to_player_seq_num);
            $ToFriendRequest->addRequest(FriendRequest::REQUEST_TYPE_RECV, $from_player_seq_num);
            // 申請数インクリメント
            $FromFriendCounter->incrRequest(true);
            // リザルトセット
            $RequestResult->setResultCode(RequestResult::COMPLETE);

        }

        // 履歴生成
        $RequestResult->createHistory();

        AppLogger::endFunc(__METHOD__);
        return $RequestResult;
    }

    /**
     * フレンド申請「承認」
     * @param int $from_player_seq_num
     * @param int $to_player_seq_num
     * @return \GameLogic\Result\Friend\AcceptResult
     */
    public static function accept(int $from_player_seq_num, int $to_player_open_id)
    {
        AppLogger::startFunc(__METHOD__, ['$from_player_seq_num' => $from_player_seq_num, '$to_player_open_id' => $to_player_open_id]);

        // オープンIDから相手のプレイヤーシーケンスNUMを取得AppLogger
        $to_player_seq_num = Player::getPlayerSeqNumByPlayerOpenId($to_player_open_id);

        // 必要インスタンス生成
        $AcceptResult      = new AcceptResult($from_player_seq_num, $to_player_seq_num); // 結果用
        $FromFriendCounter = PlayerObject::getInstance($from_player_seq_num, FriendCounter::class); // 自分用
        $FromFriendRequest = PlayerObject::getInstance($from_player_seq_num, FriendRequest::class);
        $FromFriend        = PlayerObject::getInstance($from_player_seq_num, Friend::class);
        $ToFriendCounter   = PlayerObject::getInstance($to_player_seq_num, FriendCounter::class); // 相手用
        $ToFriendRequest   = PlayerObject::getInstance($to_player_seq_num, FriendRequest::class);
        $ToFriend          = PlayerObject::getInstance($to_player_seq_num, Friend::class);

        // フレンドカウンターテーブルをロック
        FriendCounter::lockFriendCounter([$from_player_seq_num, $to_player_seq_num]);

        /*                                                  ◆ 各種チェック
          ================================================================= */
        // ▼ キャンセル済みチェック
        if ($FromFriendRequest->hasRecvRequest() === false) {
            $AcceptResult->setResultCode(AcceptResult::ALREADY_CANCEL);
            AppLogger::endFunc(__METHOD__);
            return $AcceptResult;
        }
        // ▼ 自分のフレンド枠がいっぱい
        if ($FromFriendCounter->isFullFriend() === true) {
            $AcceptResult->setStatus(AcceptResult::MY_FRIEND_IS_FULL);
            AppLogger::endFunc(__METHOD__);
            return $AcceptResult;
        }
        // ▼ 相手のフレンド枠がいっぱい
        if ($ToFriendCounter->isFullFriend() === true) {
            $AcceptResult->setStatus(AcceptResult::TO_FRIEND_IS_FULL);
            AppLogger::endFunc(__METHOD__);
            return $AcceptResult;
        }

        /*                                                     ◆ 承認処理
          ================================================================= */
        // 申請ステータス更新
        $FromFriendRequest->updateRequest(FriendRequest::REQUEST_TYPE_RECV, $to_player_seq_num, FriendRequest::REQUEST_STATUS_ACCEPTED);
        $ToFriendRequest->updateRequest(FriendRequest::REQUEST_TYPE_SEND, $from_player_seq_num, FriendRequest::REQUEST_STATUS_ACCEPTED);
        // フレンド追加
        $FromFriend->addFriend($to_player_seq_num);
        $ToFriend->addFriend($from_player_seq_num);
        // フレンド数インクリメント
        $FromFriendCounter->incrFriend(true);
        $ToFriendCounter->incrFriend(false);
        // 申請数デクリメント
        $ToFriendCounter->decrRequest(true);
        // リザルトセット & 履歴生成
        $AcceptResult->setStatus(AcceptResult::COMPLETE);
        $AcceptResult->createHistory();

        AppLogger::endFunc(__METHOD__);
        return $AcceptResult;
    }

    /**
     * フレンド申請「却下」
     * @param int $from_player_seq_num
     * @param int $to_player_seq_num
     * @return \GameLogic\Result\Friend\RejectResult
     */
    public static function reject(int $from_player_seq_num, int $to_player_open_id)
    {
        AppLogger::startFunc(__METHOD__, ['from_player_seq_num' => $from_player_seq_num, '$to_player_open_id' => $to_player_open_id]);

        // オープンIDか相手のプレイヤーシーケンスNUMを取得AppLogger
        $to_player_seq_num = Player::getPlayerSeqNumByPlayerOpenId($to_player_open_id);

        // 必要インスタンス生成
        $RejectResult      = new RejectResult($from_player_seq_num, $to_player_seq_num);
        $FromFriendCounter = PlayerObject::getInstance($from_player_seq_num, FriendCounter::class); // 自分用
        $FromFriendRequest = PlayerObject::getInstance($from_player_seq_num, FriendRequest::class);
        $ToFriendCounter   = PlayerObject::getInstance($to_player_seq_num, FriendCounter::class);   // 相手用
        $ToFriendRequest   = PlayerObject::getInstance($to_player_seq_num, FriendRequest::class);

        // フレンド状態ロック
        FriendCounter::lockFriendCounter([$from_player_seq_num, $to_player_seq_num]);

        /*                                                  ◆ 各種チェック
          ================================================================= */
        // ▼ キャンセル済みチェック
        if ($FromFriendRequest->hasRecvRequest() === false) {
             $RejectResult->setResultCode(AcceptResult::ALREADY_CANCEL);
            AppLogger::endFunc(__METHOD__);
             return $AcceptResult;
        }

        /*                                                     ◆ 却下処理
          ================================================================= */
        // 申請ステータス更新
        $FromFriendRequest->updateRequest(FriendRequest::REQUEST_TYPE_RECV, $to_player_seq_num, FriendRequest::REQUEST_STATUS_REJECTED);
        $ToFriendRequest->updateRequest(FriendRequest::REQUEST_TYPE_SEND, $from_player_seq_num, FriendRequest::REQUEST_STATUS_REJECTED);
        $ToFriendCounter->decrRequest(true); // 申請数デクリメント
        // リザルトセット & 履歴生成
        $RejectResult->setRequltCode(RejectResult::COMPLETE);
        $RejectResult->createHistory();

        AppLogger::endFunc(__METHOD__);
        return $RejectResult;
    }

    /**
     * フレンド申請「キャンセル」
     * @param int $from_player_seq_num
     * @param int $to_player_seq_num
     * @return \GameLogic\Result\Friend\CancelResult
     */
    public static function cancel(int $from_player_seq_num, int $to_player_seq_num)
    {
        AppLogger::startFunc(__METHOD__, ['from_player_seq_num' => $from_player_seq_num, '$to_player_seq_num' => $to_player_seq_num]);

        // オープンIDから相手のプレイヤーシーケンスNUMを取得AppLogger
        $to_player_seq_num = Player::getPlayerSeqNumByPlayerOpenId($to_player_open_id);

        // 必要インスタンス生成
        $CancelResult      = new CancelResult($from_player_seq_num, $to_player_seq_num);
        $FromFriendCounter = PlayerObject::getInstance($from_player_seq_num, FriendCounter::class); // 自分用
        $FromFriendRequest = PlayerObject::getInstance($from_player_seq_num, FriendRequest::class);
        $ToFriendCounter   = PlayerObject::getInstance($to_player_seq_num, FriendCounter::class);   // 相手用
        $ToFriendRequest   = PlayerObject::getInstance($to_player_seq_num, FriendRequest::class);

        // フレンド状態ロック
        FriendCounter::lockFriendCounter([$from_player_seq_num, $to_player_seq_num]);

        /*                                                  ◆ 各種チェック
          ================================================================= */
        // ▼ フレンド成立済みチェック
        if ($FromFriendRequest->hasSendRequest() === false) {
            $CancelResult->setResultCode(CancelResult::ALREADY_FRIEND);
            AppLogger::endFunc(__METHOD__);
            return $CancelResult;
        }

        /*                                                ◆ キャンセル処理
          ================================================================= */
        // 申請ステータス更新
        $FromFriendRequest->updateRequest(FriendRequest::REQUEST_TYPE_RECV, $to_player_seq_num, FriendRequest::REQUEST_STATUS_CANCELED);
        $ToFriendRequest->updateRequest(FriendRequest::REQUEST_TYPE_SEND, $from_player_seq_num, FriendRequest::REQUEST_STATUS_CANCELED);
        $FromFriendCounter->decrRequest(true); // 申請数デクリメント
        // リザルトセット & 履歴生成
        $CancelResult->setResultCode(CancelResult::COMPLETE);
        $CancelResult->createHistory();

        AppLogger::endFunc(__METHOD__);
        return $CancelResult;
    }

    /**
     * フレンド削除
     * @param int $player_seq_num
     * @param int $friend_seq_num
     * @return \GameLogic\Result\Friend\RemoveResult
     */
    public static function remove(int $player_seq_num, $friend_seq_num)
    {
        AppLogger::startFunc(__METHOD__, ['from_player_seq_num' => $from_player_seq_num, '$to_player_seq_num' => $to_player_seq_num]);

        // オープンIDから相手のプレイヤーシーケンスNUMを取得AppLogger
        $to_player_seq_num = Player::getPlayerSeqNumByPlayerOpenId($to_player_open_id);

        // 必要インスタンス生成
        $RemoveResult = new RemoveResult($from_player_seq_num, $to_player_seq_num);
        $FromFriendCounter = PlayerObject::getInstance($from_player_seq_num, FriendCounter::class); // 自分用
        $FromFriend        = PlayerObject::getInstance($from_player_seq_num, Friend::class);
        $ToFriendCounter   = PlayerObject::getInstance($to_player_seq_num, FriendCounter::class);   // 相手用
        $ToFriend          = PlayerObject::getInstance($to_player_seq_num, Friend::class);

        // フレンド状態ロック
        FriendCounter::lockFriendCounter([$from_player_seq_num, $to_player_seq_num]);

        /*                                                  ◆ 各種チェック
          ================================================================= */
        // ▼ 既にフレンドじゃない
        if ($FromFriend->isFriend($to_player_seq_num) === false) {
            $RemoveResult->setResultCode(RemoveResult::ALREADY_NOT_FRIEND);
            AppLogger::endFunc(__METHOD__);
            return $RemoveResult;
        }

        /*                                                      ◆ 削除処理
          ================================================================= */
        // フレンドステータス更新
        $FromFriend->updateFriend($to_player_seq_num, Friend::FRIEND_STATUS_REMOVED);
        $ToFriend->updateFriend($from_player_seq_num, Friend::FRIEND_STATUS_REMOVED);
        $FromFriendCounter->decrFriend(true); // フレンド数デクリメント
        $ToFriendCounter->decrFriend(true);
        // リザルトセット & 履歴生成
        $RemoveResult->setResultCode(RemoveResult::COMPLETE);
        $RemoveResult->createHistory();

        AppLogger::endFunc(__METHOD__);
        return $RemoveResult;
    }

}
