<?php


namespace mine_deep_rock\pmmp\scoreboard;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
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
        $equipments = PlayerEquipmentsDAO::get($player->getName());
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, TextFormat::BOLD . TextFormat::YELLOW . "装備:", 0, 0),
            new Score($slot, TextFormat::BOLD . "> 兵科:" . $equipments->getMilitaryDepartment()->getNameJp(), 1, 1),
            new Score($slot, TextFormat::BOLD . "> メイン:" . $equipments->getMainGunName(), 2, 2),
            new Score($slot, TextFormat::BOLD . "> サブ:" . $equipments->getSubGunName(), 3, 3),
            new Score($slot, "", 4, 4),
            new Score($slot, TextFormat::BOLD . TextFormat::YELLOW . "ステータス:", 5, 5),
            new Score($slot, TextFormat::BOLD ."> レベル:" .  $status->getLevel()->getRank(), 6, 6),
            new Score($slot, TextFormat::BOLD ."> 所持金:" .  $status->getMoney(), 7, 7),
            new Score($slot, "----------------------", 8, 8),
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