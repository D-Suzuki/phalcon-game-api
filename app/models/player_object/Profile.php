<?php

namespace PlayerObject;

use Beans\Db\ProfileBean;
use Db\PlayerDb\ProfileTbl;
use Db\ShareDb\PlayerJoinTbl;

Class Profile extends PlayerObject
{

    const MAX_LENGTH_NICKNAME = 10;

    /**
     * プロフィールBean
     * @var \Beans\ProfileBean
     */
    private $ProfileBean = null;

    /**
     * ニックネーム変更
     * @param string $nickname
     */
    public function rename(string $nickname)
    {
        \AppLogger::startFunc(__METHOD__, ['nickname' => $nickname]);
        // Bean更新
        $ProfileBean = $this->getProfileBean();
        $ProfileBean->setNickname($nickname);
        $ProfileBean->setUpdateFlg(true);
        // DB更新
        $ProfileTbl = \Db\Factory::getInstance(ProfileTbl::class, $this->player_seq_num);
        $ProfileTbl->insertOrUpdate([$ProfileBean->toRecord()]);
		$PlayerJoinTbl = \Db\Factory::getInstance(PlayerJoinTbl::class, $player_seq_num);
		$PlayerJoinTbl->updateNickname($player_seq_num, $ProfileBean->getNickname());
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * DB同期
     */
    public function syncdb()
    {
        \AppLogger::startFunc(__METHOD__);
        $ProfileBean = $this->getProfileBean();
        if ($ProfileBean->isUpdate() === true) {
            $ProfileTbl = \Db\Factory::getInstance(ProfileTbl::class, $this->player_seq_num);
            $ProfileTbl->insertOrUpdate([$ProfileBean->toRecord()]);
            $PlayerJoinTbl = \Db\Factory::getInstance(PlayerJoinTbl::class, $player_seq_num);
            $PlayerJoinTbl->updateNickname($player_seq_num, $ProfileBean->getNickname());
        }
        \AppLogger::endFunc(__METHOD__);
    }

    /**
     * プロフィールBean取得
     * @return \Beans\ProfileBean
     */
    public function getProfileBean()
    {
        \AppLogger::startFunc(__METHOD__);
        if (is_null($this->ProfileBean) === true) {
            $this->setProfileBean();
        }
        \AppLogger::endFunc(__METHOD__);
        return $this->ProfileBean;
    }

    /**
     * プロフィールBeanセット
     * @return \Beans\ProfileBean;
     */
    private function setProfileBean()
    {
        \AppLogger::startFunc(__METHOD__);
        $ProfileTbl = \Db\Factory::getInstance(ProfileTbl::class, $this->player_seq_num);
        $record     = $ProfileTbl->findByPk($this->player_seq_num);
        $this->ProfileBean = new ProfileBean($record);
        \AppLogger::endFunc(__METHOD__);
    }

}