<?php

namespace Takajo\Bootstrap;

use Phalcon\Di\FactoryDefault as FactoryDefault;

class Service {

    private $Di;

    /**
     * DIコンテナのデフォルト設定
     */
    public function createDefaultDi(\Phalcon\Config $Config) {
        
        $Di = new FactoryDefault();
#######
# LOG #
#######
        if ($Config->offsetExists('fileLogger')) {
            $Di->set('fileLogger', function () use ($Config) {
                $loggerObj = new \Phalcon\Logger\Adapter\File($Config->fileLogger->filePath . '-' . date('Ymd') . '.log');
                $loggerObj->setLogLevel($Config->fileLogger->logLevel);
                return $loggerObj;
            });
        }

#######
# URL #
#######
        if ($Config->offsetExists('url')) {
            $Di->set('url', function () use ($Config) {
                $urlObj = new UrlResolver();
                $urlObj->setBaseUri($Config->services->url->baseUri);
                return $urlObj;
            }, true);
        }
########
# View #
########
        if ($Config->offsetExists('view')) {
            $Di->set('view', function() {
                $view = new \Phalcon\Mvc\View();
                if (isset($Config->view->viewsDir)) {
                    $view->setViewsDir($Config->view->viewsDir);
                }
                return $view;
            });
        }

############
# Response #
############
        if ($Config->offsetExists('response')) {
            $Di->set('response', function() use($Config) {
                $response = new \Phalcon\Http\Response();
                $response->setContentType($Config->response->contentType->mimeType, $Config->response->contentType->charset);
                return $response;
            });
        }

##########
# DB関連 #
##########
        if ($Config->offsetExists('db')) {
            // DB Manager
            $Di->setShared('dbManager', function() use($Config) {
                $dbManagerObj = new \Takajou\Db\Manager($Config->db);
                return $dbManagerObj;
            });

            // DB Access
            $Di->setShared('dbAccess', function() use($Di) {
                $dbManagerObj = $Di->getShared('dbManager');
                $dbAccessObj  = new \Takajou\Db\Access($Di, $dbManagerObj);
                return $dbAccessObj;
            });

            // DB Connection
            foreach($Config->db as $clusterMode => $databases) {
                foreach($databases as $dbCode => $dbConfig) {
                    $Di->set($dbConfig->diName, function () use($Di, $Config, $dbConfig) {
                        // 接続情報
                        $descriptor = array(
                            'host'     => $dbConfig->host,
                            'username' => $dbConfig->username,
                            'password' => $dbConfig->password,
                            'dbname'   => $dbConfig->dbname,
                            'charset'  => $dbConfig->charset,
                            'port'     => $dbConfig->port,
                        );

                        if ( $Config->offsetExists('pdo_options') === TRUE ) {
                            $descriptor['options'] = $Config->pdo_options->toArray();
                        }

                        // DB接続
                        $connectionObj = new \Takajou\Db\Adapter\Pdo\Mysql($descriptor);
                        // DBリスナーを生成
                        $dbManagerObj  = $Di->getShared('dbManager');
                        $loggerObj     = new \Phalcon\Logger\Adapter\File($dbConfig->logPath . '/' . $dbConfig->logFile);
                        $dbListenerObj = new \Takajou\Db\Listener($dbManagerObj, $dbConfig, $loggerObj);

                        // DBコネクションのイベントマネージャを登録
                        $eventsManager = new \Phalcon\Events\Manager();
                        $eventsManager->attach('db', $dbListenerObj);
                        $connectionObj->setEventsManager($eventsManager);

                        // 初期DB接続時イベント発火
                        $connectionObj->getEventsManager()->fire('db:afterConnect', $connectionObj);

                        return $connectionObj;
                    });
                }
            }
        }
        $Di->set('sqlBuilder', function() {
            return new \Takajou\SqlBuilder\FluentPDO();
        });
        /** $Di->set('config', function () use ($Config) {
            return $Config;
        }); */
##############
# dispatcher #
##############
        if ($Config->offsetExists('dispatcher')) {
            $Di->set('dispatcher', function() use($Config) {
                $eventsManager = new \Phalcon\Events\Manager();
                $eventsManager->attach('dispatch:beforeException', function($event, $Dispatcher, $exception) use($Config) {
                // 404ページルーティングイベントを登録
                if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
                    switch ($exception->getCode()) {
                        case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        // 404ページへ遷移
                        $Dispatcher->forward(array(
                            'controller' => $Config->dispatcher->controller_404,
                            'action'     => $Config->dispatcher->action_404,
                        ));
                        return false;
                    }
                }
            });
            //Dispatcherの基本動作を設定
            $Dispatcher = new \Phalcon\Mvc\Dispatcher();
            $Dispatcher->setEventsManager($eventsManager);
            return $Dispatcher;
            });
        }

        $this->di = $Di;
        return $Di;
    }
    public function setDI($name, $definition, $shared = false) {
        if(!$Di = $this->getDI()) {
            $Di = new \Phalcon\DI\FactoryDefault();
        }
        $Di->set($name, $definition, $shared);
    }

    public function getDI() {
        return $this->di;
    }
}