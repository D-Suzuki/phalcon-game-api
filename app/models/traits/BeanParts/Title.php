<?php

namespace Traits\BeanParts;

Trait Title
{

    /**
     * タイトル
     * @var string
     */
    protected $title = 0;

    /**
     * タイトルセット
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * タイトル取得
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

}