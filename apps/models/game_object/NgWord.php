<?php

namespace GameObject;

Abstract Class NgWord
{

    /**
     * NGワード判定
     * true  -> NGワードが含まれていない
     * false -> NGワードが含まれている
     * @param string $check_word
     * @return bool
     */
    public static function isPassed(string $check_word)
    {
        \AppLogger::startFunc(__METHOD__, ['$BaseTime' => $BaseTime, '$CheckTime' => $CheckTime, '$cycle_time' => $cycle_time]);

        \AppLogger::endFunc(__METHOD__, $in_cycle);
		return true;
    }

}