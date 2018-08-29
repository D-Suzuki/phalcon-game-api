<?php

namespace Controllers\Game;

cass PlayerController extends BaseController
{

    /**
     * プレイヤー生成
     */
    public function createAction()
    {
        AppLogger::startFunc(__METHOD__);
        $CreateResult = PlayerLogic::create();

        // レスポンス生成
        $this->setResponseData([
            'result'  => $CreateResult->getResultForClient(),
            'player'  => PlayerLogic::getPlayerForClient($this->player_seq_num),
            'profile' => ProfileLogic::getProfileForClient($this->player_seq_num),
            'life'    => LifeLogic::getLifeForClient($this->player_seq_num),
            'jewel'   => JewelLogic::getJewelForClient($this->player_seq_num),
            'coin'    => CoinLogic::getCoinForClient($this->player_seq_num),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

    /**
     * プレイヤー認証
     */
    public function authAction()
    {
        AppLogger::startFunc(__METHOD__);
        // リクエストパラメータ処理
        $auth_key = $this->getRequestParameter('auth_key');
        $password = $this->getRequestParameter('password');

        // オブジェクト生成
        $AuthResult = PlayerLogic::auth($auth_key, $password);

        // レスポンス生成
        $response = [
            'result_auth' => $AuthResult->getResultForClient(),
        ];
        AppLogger::endFunc(__METHOD__);
    }

    /**
     * プレイヤーシンク
     */
    public function infoAction()
    {
        AppLogger::startFunc(__METHOD__);
        // レスポンス生成

        $this->setResponseData([
            'player'     => PlayerLogic::getPlayerForClient($this->player_seq_num),
            'profile'    => ProfileLogic::getProfileForClient($this->player_seq_num),
            'life'       => LifeLogic::getLifeForClient($this->player_seq_num),
            'jewel'      => JewelLogic::getJewelForClient($this->player_seq_num),
            'coin'       => CoinLogic::getCoinForClient($this->player_seq_num),
            'chara_list' => CharaLogic::getCharaListForClient($this->player_seq_num),
            'item_list'  => ItemLogic::getItemListForClient($this->player_seq_num),
        ]);
        AppLogger::endFunc(__METHOD__);
    }

}
