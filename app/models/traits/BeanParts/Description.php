<?php

namespace Traits\BeanParts;

Trait Description
{

    /**
     * 説明
     * @var string
     */
    protected $description = 0;

    /**
     * 説明セット
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * 説明取得
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

}