<?php


namespace mine_deep_rock\pmmp\scoreboard;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\Score;
use scoreboard_builder\Scoreboard;
use scoreboard_builder\ScoreboardSlot;
use scoreboard_builder\ScoreSortType;
use team_game_system\model\Game;

class OneOnOneScoreboard extends Scoreboard
{
    private static function create(Game $game): Scoreboard {
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score("----OneOnOne----"),
            new Score(TextFormat::YELLOW . "Map:"),
            new Score(TextFormat::BOLD . "> " . $game->getMap()->getName()),
            new Score(TextFormat::LIGHT_PURPLE . ""),
            new Score(TextFormat::LIGHT_PURPLE . "Score:"),
        ];

        foreach ($game->getTeams() as $team) {
            $maxScoreAsStr = $game->getMaxScore()->getValue() ?? "";

            $scores[] = new Score(
                $slot,
                TextFormat::BOLD . "> " . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . ": " . $team->getScore()->getValue() . " / " . $maxScoreAsStr
            );
        }

        $scores[] = new Score("----------------------");

        return parent::__create($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge(), true);
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