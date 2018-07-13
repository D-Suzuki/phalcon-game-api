<?php
namespace Db\PlayerDb;

Class GachaCounterTbl extends \Db\PlayerDb
{
    
    /**
     * テーブル名
     * @var string
     */
    public static $table_name = 'gacha_counter';
    
    /**
     * カラムリスト
     * @var array
     */
    public static $column_list = [
        'player_seq_num',
        'gacha_id',
        'gacha_menu_id',
        'total_play',
        'daily_play',
    ];

}