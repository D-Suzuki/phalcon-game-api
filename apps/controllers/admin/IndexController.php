<?php

namespace Controllers\Admin;

use PlayerObject\PlayerObject;
use Logger\AppLogger;

class IndexController extends \Phalcon\Mvc\Controller
{

    /**
     * アチーブリスト取得
     */
    public function indexAction()
    {
        AppLogger::startFunc(__METHOD__);
        if (count($this->dispatcher->getParams()) > 0) {
            $resource = implode('/', $this->dispatcher->getParams());
        } else {
            $resource = 'index.html';
        }


        $html_path = BASE_PATH . '/../admin_html/' . $resource;

        AppLogger::endFunc(__METHOD__);
        echo file_get_contents($html_path);exit;
    }

}
