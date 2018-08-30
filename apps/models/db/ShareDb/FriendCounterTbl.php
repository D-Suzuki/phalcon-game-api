<?php

namespace Db\ShareDb;

Class FriendCounterTbl extends \Db\ShareDb
{
    public static $table_name = 'friend_counter';

    public static $column_list = [
        'player_seq_num',
        'friend_count',
        'request_count',
        'created_at',
        'updated_at',
    ];

    public function selectForUpdate(array $player_seq_num_list)
    {
        \AppLogger::startFunc(__METHOD__, $player_seq_num_list);
        // クエリ生成
        $target_table = self::getTargetTable();
        $in_phrase    = parent::makeInPhrase($player_seq_num_list);
        $query        =
<<< EOF
    SELECT * FROM {$target_table}
    WHERE player_seq_num IN {$in_phrase}
    FOR UPDATE
EOF;

        // クエリ実行
        foreach ($player_seq_num_list as $player_seq_num_list) {
            parent::addBindParam($player_seq_num_list);
        }
        parent::setQuery($query);
        \AppLogger::execQuery('SELECT');
        $record_list = parent::select();
        \AppLogger::endFunc(__METHOD__);
        return $record_list;
    }

}