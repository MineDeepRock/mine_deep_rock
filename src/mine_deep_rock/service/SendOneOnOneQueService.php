<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\OneOnOneQue;
use mine_deep_rock\store\OneOnOneQuesStore;

class SendOneOnOneQueService
{
    static function execute(OneOnOneQue $que): void {
        if (OneOnOneQuesStore::findByOwnerName($que->getOwnerName()) !== null) return;
        if (OneOnOneQuesStore::findByReceiverName($que->getOwnerName()) !== null) return;

        if (OneOnOneQuesStore::findByOwnerName($que->getReceiverName()) !== null) return;
        if (OneOnOneQuesStore::findByReceiverName($que->getReceiverName()) !== null) return;
        
        OneOnOneQuesStore::add($que);
    }
}