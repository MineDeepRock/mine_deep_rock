<?php


namespace mine_deep_rock\pmmp\scoreboard;


use mine_deep_rock\model\DominationFlag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\Score;
use scoreboard_builder\Scoreboard;
use scoreboard_builder\ScoreboardSlot;
use scoreboard_builder\ScoreSortType;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class DominationScoreboard extends Scoreboard
{
    /**
     * @param Game $game
     * @param DominationFlag[] $flags
     * @return Scoreboard
     */
    private static function create(Game $game, array $flags): Scoreboard {
        $scores = [
            new Score("----Domination----"),
            new Score(TextFormat::YELLOW . "Map:"),
            new Score(TextFormat::BOLD . "> " . $game->getMap()->getName()),
            new Score(""),
            new Score(TextFormat::LIGHT_PURPLE . "Score:"),
        ];

        foreach ($game->getTeams() as $team) {
            $maxScoreAsStr = $game->getMaxScore()->getValue() ?? "";

            $scores[] = new Score(TextFormat::BOLD . "> " . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . ": " . $team->getScore()->getValue() . " / " . $maxScoreAsStr);

        }
        $scores[] = new Score("");
        $scores[] = new Score(TextFormat::GREEN . "Flags:");

        foreach ($flags as $flag) {
            $gauge = $flag->getGauge();

            if ($gauge->isEmpty()) {
                $scores[] = new Score(TextFormat::BOLD . "> " . $flag->getName() . ": 0");
            } else {
                if ($gauge->isOccupied()) {
                    $team = TeamGameSystem::getTeam($game->getId(), $gauge->getOccupyingTeamId());
                    $scores[] = new Score(TextFormat::BOLD . "> " . $team->getTeamColorFormat() . $flag->getName() . ": " . $gauge->asInt() . " / 100");
                } else if ($gauge->isOwned()) {
                    $team = TeamGameSystem::getTeam($game->getId(), $gauge->getOwingTeamId());
                    $scores[] = new Score(TextFormat::BOLD . "> " . $flag->getName() . ": " . $team->getTeamColorFormat() . $gauge->asInt() . " / 100");
                }
            }
        }

        $scores[] = new Score("------------------");

        return parent::__create(ScoreboardSlot::sideBar(), "MineDeepRock", $scores, ScoreSortType::smallToLarge(), true);
    }

    static function send(Player $player, Game $game, array $flags) {
        $scoreboard = self::create($game, $flags);
        parent::__send($player, $scoreboard);
    }

    static function update(Player $player, Game $game, array $flags) {
        $scoreboard = self::create($game, $flags);
        parent::__update($player, $scoreboard);
    }
}