<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\PlayerLevel;
use mine_deep_rock\model\PlayerStatus;

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
            if($rank === 2) {
                $nextXP = 1500 + $difference;
            } else  {
                $nextXP = 2*($rank-2)*1000 + ($rank-1)*500 + $difference;
            }
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
    }
}