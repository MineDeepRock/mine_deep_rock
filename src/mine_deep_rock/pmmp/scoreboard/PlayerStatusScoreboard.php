<?php


namespace mine_deep_rock\pmmp\scoreboard;


use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_system\models\Score;
use scoreboard_system\models\Scoreboard;
use scoreboard_system\models\ScoreboardSlot;
use scoreboard_system\models\ScoreSortType;

class PlayerStatusScoreboard extends Scoreboard
{
    private static function create(Player $player): Scoreboard {
        $status = PlayerStatusDAO::get($player->getName());
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, "======================", 0, 0),
            new Score($slot, "兵科:" . TextFormat::BOLD . $status->getMilitaryDepartment()->getNameJp(), 1, 1),
            new Score($slot, "メインウェポン:" . TextFormat::BOLD . $status->getMainGunName(), 2, 2),
            new Score($slot, "サブウェポン:" . TextFormat::BOLD . $status->getSubGunName(), 3, 3),
            new Score($slot, "Money:" . TextFormat::BOLD . $status->getMoney(), 4, 4),
        ];
        return parent::__create($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge());
    }

    static function send(Player $player) {
        $scoreboard = self::create($player);
        parent::__send($player, $scoreboard);
    }

    static function update(Player $player) {
        $scoreboard = self::create($player);
        parent::__update($player, $scoreboard);
    }
}