<?php


namespace mine_deep_rock\pmmp\scoreboard;


use mine_deep_rock\model\DominationFlag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_system\models\Score;
use scoreboard_system\models\Scoreboard;
use scoreboard_system\models\ScoreboardSlot;
use scoreboard_system\models\ScoreSortType;
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
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, "----Domination----"),
            new Score($slot, TextFormat::YELLOW . "Map:"),
            new Score($slot, TextFormat::BOLD . "> " . $game->getMap()->getName()),
            new Score($slot, ""),
            new Score($slot, TextFormat::LIGHT_PURPLE . "Score:"),
        ];

        foreach ($game->getTeams() as $team) {
            $maxScoreAsStr = $game->getMaxScore()->getValue() ?? "";

            $scores[] = new Score($slot,
                TextFormat::BOLD . "> " . $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . ": " . $team->getScore()->getValue() . " / " . $maxScoreAsStr);

        }
        $scores[] = new Score($slot, "");
        $scores[] = new Score($slot, TextFormat::GREEN . "Flags:");

        foreach ($flags as $flag) {
            $gauge = $flag->getGauge();

            if ($gauge->isEmpty()) {
                $scores[] = new Score($slot, TextFormat::BOLD . "> " . $flag->getName() . ": 0");
            } else {
                if ($gauge->isOccupied()) {
                    $team = TeamGameSystem::getTeam($game->getId(), $gauge->getOccupyingTeamId());
                    $scores[] = new Score($slot, TextFormat::BOLD . "> " . $team->getTeamColorFormat() . $flag->getName() . ": " . $gauge->asInt() . " / 100");
                } else if ($gauge->isOwned()) {
                    $team = TeamGameSystem::getTeam($game->getId(), $gauge->getOwingTeamId());
                    $scores[] = new Score($slot, TextFormat::BOLD . "> " . $flag->getName() . ": " . $team->getTeamColorFormat() . $gauge->asInt() . " / 100");
                }
            }
        }

        $scores[] = new Score($slot, "------------------");

        return parent::__create($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge(), true);
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