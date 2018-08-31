<?php

namespace Phalbase\Controller;

class BaseController extends \Phalcon\Mvc\Controller
{

    // レスポンスタイプ
    const RESPONSE_TYPE_JSON  = 1;
    const RESPONSE_TYPE_JSONP = 2;
    const RESPONSE_TYPE_DATA  = 3;

    // デフォルトコンテンツタイプ
    const DEFAULT_JSON_CONTENT_TYPE  = 'Content-type: application/json; charset=UTF-8';
    const DEFAULT_JSONP_CONTENT_TYPE = 'Content-type: application/javascript; charset=UTF-8';
    const DEFALUT_DATA_CONTENT_TYPE  = 'Content-type: image/*';

    // デフォルト文字コード
    const DEFAULT_CHARSET = 'UTF-8';

	/**
     * レスポンスタイプ
     * @var int
     */
    private $response_type = self::RESPONSE_TYPE_JSON;

    /**
     * レスポンスデータ
     * @var array
     */
    private $response_data = [];

    /**
     * コールバック
     * @var string
     */
    private $callback      = NULL;

    /**
     * 初期化
     */
    public function initialize()
    {
        // callbackパラメータが設定されていた場合
        $callback = $this->request->get('callback');
        if (strlen($callback) > 0) {
            $this->setResponseType(self::RESPONSE_TYPE_JSONP);
            $this->setCallback($callback);
        }
    }

    /**
     * レスポンスタイプへのsetter
     * @param int $response_type
     */
    protected function setResponseType($response_type)
    {
        $this->response_type = $response_type;
    }

    /**
     * レスポンスタイプへのgetter
     */
    protected function getResponseType()
    {
        return $this->response_type;
    }

    /**
     * レスポンスデータへのsetter
     * @param int $response_type
     */
    protected function setResponseData(array $response_data)
    {
        $this->response_data = $response_data;
    }

    /**
     * レスポンスデータへのgetter
     */
    protected function getResponseData()
    {
        return $this->response_data;
    }

    /**
     * コールバック名へのsetter
     * @var string
     */
    protected function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * レスポンス返却
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     */
    public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher)
    {
        switch($this->getResponseType()) {
            case self::RESPONSE_TYPE_JSON:
                $this->response->setContentType(self::DEFAULT_JSON_CONTENT_TYPE, self::DEFAULT_CHARSET);
                $this->response->setJsonContent($this->response_data);
                break;
            case self::RESPONSE_TYPE_JSONP:
                $this->response->setContentType(self::DEFAULT_JSONP_CONTENT_TYPE, self::DEFAULT_CHARSET);
                $this->response->setJsonContent($this->response_data);
                $this->response->setContent($this->callback . '(' . $this->response->getContent() . ')');
                break;
            case self::RESPONSE_TYPE_DATA:
                $this->response->setContentType(self::DEFALUT_DATA_CONTENT_TYPE, self::DEFAULT_CHARSET);
                $this->response->setContent($this->response_data);
                break;
        }

        $this->response->send();
    }
}
