<?php
namespace Db\PlayerDb;

Class PlayerTbl extends \Db\PlayerDb
{

    /**
     * テーブル名
     * @var string
     */
    public static $table_name = 'player';

    /**
     * カラムリスト
     * @var array
     */
    public static $column_list = [
        'player_seq_num',
        'exp',
        'status',
        'chara_limit',
        'friend_limit',
    ];

}