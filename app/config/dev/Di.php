<?php

namespace Config;

Class Di extends \Phalcon\Config
{

    /**
     * 設定リスト
     * @var array
     */
    public static $settings = [
        'url' => [
            'baseUri' => '/',
        ],
        'view' => [
            'viewsDir' => '/',
        ],
    ];

}