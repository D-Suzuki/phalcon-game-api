<?php

namespace Db\PlayerDb;

Class FriendTbl extends \Db\PlayerDb
{

    public static $table_name = 'friend';

    public static $column_list = [
        'player_seq_num',
        'friend_player_seq_num',
        'status',
        'created_at',
        'updated_at',
    ];

}