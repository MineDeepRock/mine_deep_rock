<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerLevel;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\pmmp\event\PlayerLevelUpEvent;

class GivePlayerXpService
{
    static function execute(string $name, int $amount): void {
        $status = PlayerStatusDAO::get($name);
        $level = $status->getLevel();
        $rank = $level->getRank();
        $nextXP = $level->getXpToNextLevel();
        $totalXp = $level->getTotalXp() + $amount;

        $difference = $nextXP - $amount;
        if ($difference <= 0) {
            $rank++;
            $nextXP = 1000*1.3**($rank-1) + $difference;
        }

        $playerLevel = new PlayerLevel(
            $status->getLevel()->getRank(),
            $totalXp,
            $nextXP
        );

        PlayerStatusDAO::update(new PlayerStatus(
                $name,
                $playerLevel,
                $status->getMoney(),
                $status->getOwningSkills()
            )
        );


        if ($difference <= 0) {
            $event = new PlayerLevelUpEvent(new PlayerStatus(
                $name,
                $playerLevel,
                $status->getMoney(),
                $status->getOwningSkills()
            ));
            $event->call();
        }
    }
}