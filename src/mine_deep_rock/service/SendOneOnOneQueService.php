<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\OneOnOneQue;
use mine_deep_rock\store\OneOnOneQuesStore;

class SendOneOnOneQueService
{
    static function execute(OneOnOneQue $que): bool {
        if (OneOnOneQuesStore::findByOwnerName($que->getOwnerName()) !== null) return false;
        if (OneOnOneQuesStore::findByReceiverName($que->getOwnerName()) !== null) return false;

        if (OneOnOneQuesStore::findByOwnerName($que->getReceiverName()) !== null) return false;
        if (OneOnOneQuesStore::findByReceiverName($que->getReceiverName()) !== null) return false;

        OneOnOneQuesStore::add($que);
        return true;
    }
}