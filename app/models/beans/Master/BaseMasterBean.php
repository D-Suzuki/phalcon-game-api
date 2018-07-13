<?php

namespace Beans\Master;

Abstract Class BaseMasterBean extends \Beans\BaseBean
{

    /**
     * コンストラクタ
     * @param array $master
     */
    public function __construct(array $master = null)
    {
        parent::__construct($master);
    }

}