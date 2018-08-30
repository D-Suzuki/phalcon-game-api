<?php

namespace Traits\BeanParts;

Trait CreatedAt
{

    /**
     * 作成日
     * @var string
     */
    protected $created_at = null;

    /**
     * 作成日セット
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * 作成日取得
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return new \DateTime($this->created_at);
    }

}