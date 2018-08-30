<?php

namespace Beans;

Abstract Class BaseBean
{

    /**
     * コンストラクタ
     * @param array $property_list
     */
    public function __construct(array $property_list = null)
    {
        if (is_null($property_list) === false){
            $this->setProperties($property_list);
        }
    }

    /**
     * 複数プロパティを一括でセット
     * @param array $property_list
     */
    protected function setProperties(array $property_list)
    {
        foreach($property_list as $key => $value){
            $this->setProperty($key, $value);
        }
    }

    /**
     * プロパティ存在確認
     * @param string $property_name
     */
    protected function hasProperty($property_name)
    {
        return property_exists($this, $property_name);
	}

    /**
     * プロパティのセッター
     * @param string $property_name
     * @param mixed $value
     */
    protected function setProperty($property_name, $value)
    {
        if ($this->hasProperty($property_name) === false) {
            throw new \Exception(static::class . ' is not exists property [ property_name : ' . $property_name . ' ]');
        }
        $this->$property_name = $value;
    }

    /**
     * プロパティのゲッター
     * @param string $property_name
     */
    protected function getProperty($property_name)
    {
        if ($this->hasProperty($property_name) === false) {
            throw new \Exception(static::class . ' is not exists propety [ property_name : ' . $property_name . ' ]');
        }
        return $this->$property_name;
    }

}