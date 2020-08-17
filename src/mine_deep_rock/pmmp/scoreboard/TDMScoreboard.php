<?php


namespace mine_deep_rock\pmmp\scoreboard;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_system\models\Score;
use scoreboard_system\models\Scoreboard;
use scoreboard_system\models\ScoreboardSlot;
use scoreboard_system\models\ScoreSortType;
use team_game_system\model\Game;

class TDMScoreboard extends Scoreboard
{
    private static function create(Game $game): Scoreboard {
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, "====TeamDeathMatch====", 0, 0),
            new Score($slot, "Map:" . $game->getMap()->getName(), 1, 1),
        ];

        $index = count($scores) - 1;
        foreach ($game->getTeams() as $team) {
            $scores[] = new Score($slot, $team->getTeamColorFormat() . $team->getName() . ":" . $team->getScore()->getValue(), $index, $index);
        }

        return parent::__create($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge());
    }

    static function send(Player $player, Game $game) {
        $scoreboard = self::create($game);
        parent::__send($player, $scoreboard);
    }

    static function update(Player $player, Game $game) {
        $scoreboard = self::create($game);
        parent::__update($player, $scoreboard);
    }
}