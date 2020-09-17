<?php


namespace mine_deep_rock\pmmp\scoreboard;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\Score;
use scoreboard_builder\Scoreboard;
use scoreboard_builder\ScoreboardSlot;
use scoreboard_builder\ScoreSortType;

class PlayerStatusScoreboard extends Scoreboard
{
    private static function create(Player $player): Scoreboard {
        $status = PlayerStatusDAO::get($player->getName());
        $equipments = PlayerEquipmentsDAO::get($player->getName());
        $scores = [
            new Score(TextFormat::RESET . "----------------------"),
            new Score(TextFormat::BOLD . TextFormat::YELLOW . "装備:"),
            new Score(TextFormat::BOLD . "> 兵科:" . $equipments->getMilitaryDepartment()->getNameJp()),
            new Score(TextFormat::BOLD . "> メイン:" . $equipments->getMainGunName()),
            new Score(TextFormat::BOLD . "> サブ:" . $equipments->getSubGunName()),
            new Score(""),
            new Score(TextFormat::BOLD . TextFormat::YELLOW . "ステータス:"),
            new Score(TextFormat::BOLD ."> レベル:" .  $status->getLevel()->getRank()),
            new Score(TextFormat::BOLD ."> 所持金:" .  $status->getMoney()),
            new Score("----------------------"),
        ];
        return parent::__create(ScoreboardSlot::sideBar(), "MineDeepRock", $scores, ScoreSortType::smallToLarge(), true);
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