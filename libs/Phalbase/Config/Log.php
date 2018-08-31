<?php

namespace Phalbase\Config;

Class Log
{

    final public function __construct($settings){
        $this->log_id    = $settings['log_id'];
        $this->file_path = $settings['file_path'];
        $this->adapter   = $settings['adapter'];
        $this->log_level = $settings['log_level'];
    }

    public $log_id = '';

    public $file_path = '';

    public $adapter = '';

    public $log_level = '';

}
