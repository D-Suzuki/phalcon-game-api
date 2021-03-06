<?php

namespace Logics;

use GameObject\Master\GachaMaster;
use GameObject\Master\GachaNormalMenuMaster;
use GameObject\Master\GachaStepUpMenuMaster;
use GameObject\Result\Lottery\DrawResult;
use GameObject\Result\Gacha\PlayResult;
use PlayerObject\PlayerObject;
use PlayerObject\GachaCounter;
use PlayerObject\Chara;
use PlayerObject\RewardBox;

Class GachaLogic
{

    /**
     * クライアント用ガチャリスト取得
     * @param int $player_seq_num
     * @return array
     */
    public static function getGachaListForClient(int $player_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num]);

        $gacha_list_for_client = [];
        $gacha_bean_list       = GachaMaster::getGachaBeanList();
        if (count($gacha_bean_list) > 0) {
            foreach ($gacha_bean_list as $GachaBean) {
                // 期間チェック
                if ($GachaBean->inPeriod() === false) {
                    continue;
                }
                // クライアント用ガチャリスト生成
                $gacha_list_for_client[] = [
                    'gacha_id'        => $GachaBean->getGachaId(),
                    'gacha_type'      => $GachaBean->getGachaType(),
                    'price_type'      => $GachaBean->getPriceType(),
                    'start_time'      => (int) $GachaBean->getStartTime()->format('U'),
                    'end_time'        => (int) $GachaBean->getEndTime()->format('U'),
                    'title'           => $GachaBean->getTitle(),
                    'description'     => $GachaBean->getDescription(),
                    'gacha_menu_list' => self::getGachaMenuListForClient($player_seq_num, $GachaBean->getGachaId()),
                ];
            }
        }

        \AppLogger::endFunc(__METHOD__, $gacha_list_for_client);
        return $gacha_list_for_client;
    }

    /**
     * ガチャ実行
     * @param int $player_seq_num
     * @param int $gacha_id
     * @param int $draw_count
     * @return \GameLogic\Result\Gacha\PlayResult
     */
    public static function play(int $player_seq_num, int $gacha_id, int $draw_count) : PlayResult
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$gacha_id' => $gacha_id, '$draw_count' => $draw_count]);

        // 必要インスタンス生成
        $PlayResult   = new PlayResult($player_seq_num, $gacha_id, $draw_count);
        $GachaCounter = PlayerObject::getInstance($player_seq_num, GachaCounter::class);
        $Chara        = PlayerObject::getInstance($player_seq_num, Chara::class);
        $RewardBox    = PlayerObject::getInstance($player_seq_num, RewardBox::class);

        /*                                                  ◆ 各種チェック
          ================================================================= */
        // ▼ ガチャID / 回数チェック
        $GachaBean     = GachaMaster::getGachaBean($gacha_id);
        $GachaMenuBean = self::getGachaMenuBean($player_seq_num, $gacha_id, $draw_count);
        if (is_null($GachaBean) === true || is_null($GachaMenuBean) === true) {
            throw new \Exception('gacha is not playable');
        }
        // ▼ 期間チェック
        if ($GachaBean->inPeriod() === false) {
            $PlayResult->setResultCode(PlayResult::NOT_IN_PERIOD);
            \AppLogger::endFunc(__METHOD__);
            return $PlayResult;
        }
        // ▼ トータル回数制限チェック
        if ($GachaMenuBean->getTotalPlayLimit() > 0
         && $GachaMenuBean->getTotalPlayLimit() <= $GachaCounter->getTotalPlay($gacha_id, $draw_count)) {
            $PlayResult->setResultCode(PlayResult::NOT_IN_TOTAL_PLAY_LIMIT);
            \AppLogger::endFunc(__METHOD__);
            return $PlayResult;
        }
        // ▼ デイリー回数制限チェック
        if ($GachaMenuBean->getDailyPlayLimit() > 0
         && $GachaMenuBean->getDailyPlayLimit() <= $GachaCounter->getDailyPlay($gacha_id, $draw_count)) {
            $PlayResult->setResultCode(PlayResult::NOT_IN_TOTAL_DAILY_LIMIT);
            \AppLogger::endFunc(__METHOD__);
            return $PlayResult;
        }

        /*                                                         ◆ 支払
          ================================================================= */
        switch ($GachaBean->getPriceType()) {
            // ジュエル
            case GachaMaster::PRICE_TYPE_JEWEL:
                $UseJewelResult = JewelLogic::useJewel($player_seq_num, $GachaMenuBean->getPrice(), JewelLogic::USE_TYPE_NORMAL, AppConst::SCENE_ID_GACHA);
                if ($UseJewelResult->getResultCode() == $UseJewelResult::IS_NOT_ENOUGH) {
                    throw new \Exception('Jewel is not enough');
                }
                break;
            // コイン
            case GachaMaster::PRICE_TYPE_COIN:
                $UseCoinResult = CoinLogic::useCoin($player_seq_num, $GachaMenuBean->getPrice(), AppConst::SCENE_ID_GACHA);
                if ($UseCoinResult->getResultCode() == $UseCoinResult::IS_NOT_ENOUGH) {
                    throw new \Exception('Coin is not enough');
                }
                break;
            default:
                throw new \Exception('price_type is not valid [price_type=' . $GachaBean->getPriceType() . ']');
        }

        /*                                                   ◆ ガチャ実行
          ================================================================= */
		// 通常抽選
		for($i = 0; $i < $GachaMenuBean->getRegularDrawCount(); $i++) {
		    $DrawResult = LotteryLogic::draw($GachaMenuBean->getLotteryId());
		    $PlayResult->addDrawResult($DrawResult);
		}
		// 確定抽選（ある場合）
		if ($GachaMenuBean->hasFixed() === true) {
			for($i = 0; $i < $GachaMenuBean->getFixedDrawCount(); $i++) {
				$DrawResult = LotteryLogic::draw($GachaMenuBean->getFixedLotteryId());
				$DrawResult->setFixedFlg(true);
				$PlayResult->addDrawResult($DrawResult);
			}
		}
		// おまけ抽選（ある場合）
		if ($GachaMenuBean->hasBonus() === true) {
            for($i = 0; $i < $GachaMenuBean->getBonusDrawCount(); $i++) {
				$DrawResult = LotteryLogic::draw($GachaMenuBean->getBonusLotteryId());
				$DrawResult->setBonusFlg(true);
				$PlayResult->addDrawResult($DrawResult);
			}
		}
        // ガチャ実行回数インクリメント
        $GachaCounter->incrGachaPlayCount($GachaMenuBean->getGachaId(), $GachaMenuBean->getGachaMenuId());

		/*                                                         ◆ 配布
          ================================================================= */
        // キャラは直接付与（溢れたら報酬BOX）
        if (count($PlayResult->getDrawedCharaIdList()) > 0) {
            $AddCharasResult = CharaLogic::addCharas($player_seq_num, $PlayResult->getDrawedCharaIdList(), AppConst::SCENE_ID_GACHA);
        }
        // キャラ溢れ分は報酬BOXへ
        $reward_list = [];
        if ($AddCharasResult->isOverflow() === true) {
            foreach ($AddCharasResult->getOverflowCharaId() as $chara_id) {
                $reward_list[] = new Reward(AppConst::OBJECT_TYPE_CHARA, $chara_id, $reward_count = 1);
            }
        }
        // アイテムは報酬BOXへ
        if (count($PlayResult->getDrawedItemList()) > 0) {
            foreach ($PlayResult->getDrawedItemList() as $item) {
                $reward_list[] = new Reward(AppConst::OBJECT_TYPE_ITEM, $item['item_id'], $item['item_count']);
            }
        }
        // まとめて報酬BOX付与実行
        if (count($reward_list) > 0) {
            RewardLogic::addRewards($player_seq_num, $reward_list, AppConst::SCENE_ID_GACHA);
        }

        // 結果コードセット & 履歴追加
        $PlayResult->setResultCode(PlayResult::COMPLETE);
        $PlayResult->createHistory();
        \AppLogger::endFunc(__METHOD__);
        return $PlayResult;
    }

    /**
     * クライアント用ガチャメニューリスト取得
     * @param int $player_seq_num
     * @param int $gacha_id
     * @return array
     */
    private static function getGachaMenuListForClient(int $player_seq_num, int $gacha_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$gacha_id' => $gacha_id]);

        $gacha_menu_list_for_client = [];

        if (GachaMaster::isValid($gacha_id) === false) {
            throw new \Exception('gacha_id is not valid [gacha_id=' . $gacha_id . ']');
        }
        $GachaBean = GachaMaster::getGachaBean($gacha_id);
        switch ($GachaBean->getGachaType()) {
            // 通常ガチャ
            case GachaMaster::GACHA_TYPE_NORAMAL:
                $gacha_menu_bean_list = GachaNormalMenuMaster::getGachaMenuBeanListByGachaId($gacha_id);
                break;
            // ステップアップガチャ
            case GachaMaster::GACHA_TYPE_STEP_UP:
                $gacha_menu_bean_list = GachaStepUpMenuMaster::getGachaMenuBeanListByGachaId($gacha_id);
                break;
            // BOXガチャ
            case GachaMaster::GACHA_TYPE_BOX:
                $gacha_menu_bean_list = GachaBoxMenuMaster::getGachaMenuBeanListByGachaId($gacha_id);
                break;
            default:
                throw new \Exception('gacha_type is not valid [gacha_type=' . $GachaBean->getGachaType() . ']');
        }
        if (empty($gacha_menu_bean_list) === true) {
            throw new \Exception('gacha_menu_bean_list is empty [gacha_id=' . $gacha_id . ']');
        }

        $GachaCounter = PlayerObject::getInstance($player_seq_num, GachaCounter::class);
        foreach ($gacha_menu_bean_list as $GachaMenuBean) {
            // ガチャタイプ毎に生成
            switch ($GachaBean->getGachaType()) {
                // 通常ガチャ
                case GachaMaster::GACHA_TYPE_NORAMAL:
                    $gacha_menu_list_for_client[] = [
                        'draw_count'       => (int) $GachaMenuBean->getDrawCount(),
                        'price'            => (int) $GachaMenuBean->getPrice(),
                        'total_play_limit' => (int) $GachaMenuBean->getTotalPlayLimit(),
                        'daily_play_limit' => (int) $GachaMenuBean->getDailyPlayLimit(),
                        'total_play'       => (int) $GachaCounter->getTotalPlay($GachaMenuBean->getGachaId(), $GachaMenuBean->getGachaMenuId()),
                        'daily_play'       => (int) $GachaCounter->getDailyPlay($GachaMenuBean->getGachaId(), $GachaMenuBean->getGachaMenuId()),
                    ];
                    break;
                // ステップアップガチャ
                case GachaMaster::GACHA_TYPE_STEP_UP:
                    $current_step = self::getCurrentStep($player_seq_num, $GachaBean->getGachaId());
                    $gacha_menu_list_for_client[] = [
                        'draw_count'       => (int) $GachaMenuBean->getDrawCount(),
                        'price'            => (int) $GachaMenuBean->getPrice(),
                        'step_count'       => (int) $GachaMenuBean->getStepCount(),
                        'is_current_step'  => (int) $GachaMenuBean->getStepCount() == $current_step ? 1 : 0,
                        'total_play_limit' => (int) $GachaMenuBean->getTotalPlayLimit(),
                        'daily_play_limit' => (int) $GachaMenuBean->getDailyPlayLimit(),
                        'total_play'       => (int) $GachaCounter->getTotalPlay($GachaMenuBean->getGachaId(), $GachaMenuBean->getGachaMenuId()),
                        'daily_play'       => (int) $GachaCounter->getDailyPlay($GachaMenuBean->getGachaId(), $GachaMenuBean->getGachaMenuId()),
                    ];
                    break;
            }
        }

        \AppLogger::endFunc(__METHOD__);
        return $gacha_menu_list_for_client;
    }

    /**
     * ガチャメニューBean取得
     * @param int $player_seq_num
     * @param int $gacha_id
     * @param int $draw_count
     * @return \Beans\MasterData\GachaMenuBean
     */
    private static function getGachaMenuBean(int $player_seq_num, int $gacha_id, int $draw_count)
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$gacha_id' => $gacha_id, '$draw_count' => $draw_count]);

        $GachaMenuBean = null;
        if (GachaMaster::isValid($gacha_id) === false) {
            throw new \Exception('gacha_id is not valid [gacha_id='. $gacha_id . ']');
        }
        $GachaBean = GachaMaster::getGachaBean($gacha_id);
        switch ($GachaBean->getGachaType()) {
            // 通常ガチャ
            case GachaMaster::GACHA_TYPE_NORAMAL:
                $GachaMenuBean = GachaNormalMenuMaster::getGachaMenuBean($gacha_id, $draw_count);
                break;
            // ステップアップガチャ
            case GachaMaster::GACHA_TYPE_STEP_UP:
                $current_step  = self::getCurrentStep($player_seq_num, $gacha_id);
                $GachaMenuBean = GachaStepUpMenuMaster::getGachaMenuBean($gacha_id, $current_step);
                break;
            default:
                throw new \Exception('gacha_type is not valid [gacha_type=' . $GachaBean->getGachaType() . ']');
        }

        \AppLogger::endFunc(__METHOD__);
        return $GachaMenuBean;
    }

    /**
     * 現在のステップ数取得
     * @param int $player_seq_num
     * @param int $gacha_id
     * @return int
     */
    private static function getCurrentStep(int $player_seq_num, int $gacha_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$player_seq_num' => $player_seq_num, '$gacha_id' => $gacha_id]);

        if (GachaMaster::isValid($gacha_id) === false) {
            throw new \Exception('gacha_id is not valid [gacha_id=' . $gacha_id . ']');
        }
        $GachaBean = GachaMaster::getGachaBean($gacha_id);
        switch ($GachaBean->getGachaType()) {
            // 通常ガチャ
            case GachaMaster::GACHA_TYPE_NORAMAL:
                $current_step = 0;
                break;
            // ステップアップガチャ
            case GachaMaster::GACHA_TYPE_STEP_UP:
                $total_step   = count(GachaStepUpMenuMaster::getGachaMenuBeanListByGachaId($gacha_id));
                $GachaCounter = PlayerObject::getInstance($player_seq_num, GachaCounter::class);
                $total_play   = $GachaCounter->getTotalPlay($gacha_id, null);
                $current_step = $total_play % $total_step + 1;
                break;
            default:
                throw new \Exception('gacha_type is not valid [gacha_type=' . $GachaBean->getGachaType() . ']');
        }

        \AppLogger::endFunc(__METHOD__);
        return $current_step;
    }

}
