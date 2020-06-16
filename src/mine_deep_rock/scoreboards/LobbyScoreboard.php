<?php


namespace mine_deep_rock\scoreboards;


use pocketmine\Player;
use scoreboard_system\models\Score;
use scoreboard_system\models\Scoreboard;
use scoreboard_system\models\ScoreboardSlot;
use scoreboard_system\models\ScoreSortType;

class LobbyScoreboard extends Scoreboard
{

    private static function create(int $participantsCount): Scoreboard {
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, "=========", 0, 0),
            new Score($slot, "参加人数:" . $participantsCount, 1, 1),
        ];
        return parent::__create($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge());
    }

    static function send(Player $player, int $participantsCount) {
        $scoreboard = self::create($participantsCount);
        parent::__send($player, $scoreboard);
    }

    static function update(Player $player, int $participantsCount) {
        $scoreboard = self::create($participantsCount);
        parent::__update($player, $scoreboard);
    }
}