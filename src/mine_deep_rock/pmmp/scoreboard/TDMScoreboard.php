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
            new Score($slot, "----TeamDeathMatch----", 0, 0),
            new Score($slot, TextFormat::YELLOW . "Map:", 1, 1),
            new Score($slot,  TextFormat::BOLD . "> " . $game->getMap()->getName(), 2, 2),
            new Score($slot, TextFormat::LIGHT_PURPLE . "", 3, 3),
            new Score($slot, TextFormat::LIGHT_PURPLE . "Score:", 4, 4),
        ];

        $index = count($scores);
        foreach ($game->getTeams() as $team) {
            $maxScoreAsStr = $game->getMaxScore()->getValue() ?? "";

            $scores[] = new Score($slot,
                TextFormat::BOLD . "> " . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . ": " . $team->getScore()->getValue() . " / " . $maxScoreAsStr,
                $index,
                $index);

            $index++;
        }

        $scores[] = new Score($slot, "----------------------", $index, $index);

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