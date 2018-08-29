<?php

namespace Traits\BeanParts;

Trait GachaMenuId
{

    /**
     * ガチャメニューID
     * @var int
     */
    protected $gacha_menu_id = null;

    /**
     * ガチャメニューIDセット
     * @param int $gacha_menu_id
     */
    public function setGachaMenuId(int $gacha_menu_id)
    {
        $this->gacha_menu_id = $gacha_menu_id;
    }

    /**
     * ガチャメニューID取得
     * @return int
     */
    public function getGachaMenuId()
    {
        return $this->gacha_menu_id;
    }

}