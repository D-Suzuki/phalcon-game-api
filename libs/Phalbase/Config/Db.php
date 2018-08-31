<?php

namespace Phalbase\Config;

class Db
{

    final public function __construct($settings){
        $this->connection_id = $settings['connection_id'];
        $this->adapter       = $settings['adapter'];
        $this->host          = $settings['host'];
        $this->username      = $settings['username'];
        $this->password      = $settings['password'];
        $this->dbname        = $settings['dbname'];
        $this->charset       = $settings['charset'];
        $this->log_id        = $settings['log_id'];
    }

    public $connection_id = '';

    public $adapter = '';

    public $host = '';

    public $user = '';

    public $password = '';

    public $dbname = '';

    public $charset = '';

    public $log_id = '';

}
