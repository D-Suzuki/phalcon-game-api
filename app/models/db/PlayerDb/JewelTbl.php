<?php
namespace Db\PlayerDb;

Class JewelTbl extends \Db\PlayerDb
{
    /**
     * テーブル名
     * @var type
     */
    public static $table_name  = 'jewel';

    /**
     * カラムリスト
     * @var array
     */
    public static $column_list = [
        'player_seq_num',
        'unit_price',
        'count',
        'updated_at',
        'created_at',
    ];
}