<?php

namespace PlayerObject;

use Beans\Db\CharaBean;
use Db\PlayerDb\CharaTbl;
use Db\ShareDb\CharaSequenceTbl;

Class Chara extends PlayerObject
{

    const STATUS_VALID = 0;

    /**
     * キャラBeanリスト
	 * @var mixed
     */
    private $chara_bean_list = null;

    /**
     * ダミーキャラシーケンスNUM
     * @var int
     */
    private $dummy_chara_seq_num = -1;

    /**
     * キャラBeanリスト取得
	 * @return array[chara_seq_num] = \Beans\Db\CharaBean
     */
    public function getCharaBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->chara_bean_list) === true) {
            $this->setCharaBeanList();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->chara_bean_list;
    }

    /**
     * 指定キャラBean取得（chara_seq_num指定）
	 * @return \Beans\Db\CharaBean
     */
    public function getCharaBean(int $chara_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['chara_seq_num' => $chara_seq_num]);
        $CharaBean       = null;
        $chara_bean_list = $this->getCharaBeanList();
        if (is_null($chara_bean_list) === false && array_key_exists($chara_seq_num, $chara_bean_list) === true) {
            $CharaBean = $chara_bean_list[$chara_seq_num];
        }
        \AppLogger::endFunc(__METHOD__);
        return $CharaBean;
    }

    /**
     * キャラ数取得
     * @return int
     */
    public function count()
    {
        return count($this->getCharaBeanList());
    }

    /**
     * キャラ追加
     * @param int $chara_id
     * @return CharaBean
     */
    public function addChara(int $chara_id)
    {
        \AppLogger::startFunc(__METHOD__, ['$chara_id' => $chara_id]);
        $CharaBean = new CharaBean([
            'chara_seq_num'  => $this->dummy_chara_seq_num,
            'player_seq_num' => $this->getPlayerSeqNum(),
            'chara_id'       => $chara_id,
            'exp'            => 0,
            'status'         => self::STATUS_VALID,
            'created_at'     => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
            'updated_at'     => \AppRegistry::getAccessTime()->format('Y-m-d H:i:s'),
        ]);
        $CharaBean->setUpdateFlg(true);
        \AppLogger::endFunc(__METHOD__);
        $this->chara_bean_list[$this->dummy_chara_seq_num] = $CharaBean;
        $this->dummy_chara_seq_num--;
        return $CharaBean;
    }

    /**
     * ステータス更新
	 * @param int $chara_seq_num
	 * @param int $status
	 * @return array
     */
    public function setStatus(int $chara_seq_num, int $status)
    {
        \AppLogger::startFunc(__METHOD__, ['$chara_seq_num' => $chara_seq_num, '$status' => $status]);
        // Bean更新
        $CharaBean = $this->getCharaBean($chara_seq_num);
        if (is_null($CharaBean) === true) {
            throw new \Exception('CharaBean is null [chara_seq_num=' . $chara_seq_num . ']');
        }
        $CharaBean->setStatus($status);
        $CharaBean->setUpdateFlg(true);
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * 限界突破
	 * @param int $chara_seq_num
	 * @return array
     */
    public function limitBreak(int $chara_seq_num)
    {
        \AppLogger::startFunc(__METHOD__, ['$chara_seq_num' => $chara_seq_num]);
        // Bean更新
        $CharaBean = $this->getCharaBean($chara_seq_num);
        if (is_null($CharaBean) === true) {
            throw new \Exception('CharaBean is null [chara_seq_num=' . $chara_seq_num . ']');
        }
        $CharaBean->setLimitBreak($CharaBean->getLimitBreak() + 1);
        $CharaBean->setUpdateFlg(true);
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * DB同期
     */
    public function syncdb()
    {
        \AppLogger::startFunc(__METHOD__);
        // 同期レコードリスト生成
        $sync_record_list = [];
        foreach ($this->getCharaBeanList() as $CharaBean) {
            if ($CharaBean->isUpdate() === true) {
                // 追加分はシーケンス番号を振る
                if ($CharaBean->getCharaSeqNum() < 0) {
                    $dummy_chara_seq_num = $CharaBean->getCharaSeqNum();
                    unset($this->chara_bean_list[$dummy_chara_seq_num]);
                    $CharaSequenceTbl = \Db\Factory::getInstance(CharaSequenceTbl::class);
                    $chara_seq_num    = $CharaSequenceTbl->insert(['chara_seq_num' => 0]);
                    $CharaBean->setCharaSeqNum($chara_seq_num);
                    $this->chara_bean_list[$chara_seq_num] = $CharaBean;
                }
                $sync_record_list[] = $CharaBean->toRecord();
            }
        }
        // DBへ同期
        if (count($sync_record_list) > 0) {
            $CharaTbl = \Db\Factory::getInstance(CharaTbl::class, $this->player_seq_num);
            $CharaTbl->insertOrUpdate($sync_record_list);
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * キャラBeanリストセット
     */
    private function setCharaBeanList()
    {
        \AppLogger::startFunc(__METHOD__);
        $CharaTbl    = \Db\Factory::getInstance(CharaTbl::class, $this->player_seq_num);
        $record_list = $CharaTbl->searchBy(['player_seq_num' => $this->player_seq_num]);
        if (is_null($record_list) === false) {
            foreach ($record_list as $record) {
                $this->chara_bean_list[$record['chara_seq_num']] = new CharaBean($record);
            }
        } else {
            $this->chara_bean_list = [];
        }
        \AppLogger::endFunc(__METHOD__);
    }

}