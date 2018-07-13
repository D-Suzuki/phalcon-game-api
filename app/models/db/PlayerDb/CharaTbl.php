<?php
namespace Db\PlayerDb;

Class CharaTbl extends \Db\PlayerDb
{

    /**
     * テーブル名
     * @var string
     */
    public static $table_name = 'chara';

    /**
     * カラムリスト
     * @var array
     */
    public static $column_list = [
        'chara_seq_num',
        'chara_id',
        'exp',
        'status',
    ];

}