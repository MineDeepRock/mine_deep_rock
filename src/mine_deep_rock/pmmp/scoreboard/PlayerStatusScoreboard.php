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
            new Score($slot, TextFormat::RESET . "----------------------"),
            new Score($slot, TextFormat::BOLD . TextFormat::YELLOW . "装備:"),
            new Score($slot, TextFormat::BOLD . "> 兵科:" . $equipments->getMilitaryDepartment()->getNameJp()),
            new Score($slot, TextFormat::BOLD . "> メイン:" . $equipments->getMainGunName()),
            new Score($slot, TextFormat::BOLD . "> サブ:" . $equipments->getSubGunName()),
            new Score($slot, ""),
            new Score($slot, TextFormat::BOLD . TextFormat::YELLOW . "ステータス:"),
            new Score($slot, TextFormat::BOLD ."> レベル:" .  $status->getLevel()->getRank()),
            new Score($slot, TextFormat::BOLD ."> 所持金:" .  $status->getMoney()),
            new Score($slot, "----------------------"),
        ];
        return parent::__create($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge(), true);
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