<?php

namespace GameObject\Sequence;

use Db\ShareDb\CharaSequenceTbl;

Class CharaSequence
{

    public static function createSeqNum()
    {
        $CharaSequenceTbl = \Db\Factory::getInstance(CharaSequenceTbl::class);
        $CharaSequenceTbl->create();
        return $CharaSequenceTbl->getLastInsertId();
    }

}
