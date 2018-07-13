<?php

namespace Db\PlayerDb;

Class ProfileTbl extends \Db\PlayerDb
{
    public static $table_name = 'profile';

    public static $column_list = [
        'player_seq_num',
        'nickname',
        'created_at',
        'updated_at',
    ];
}