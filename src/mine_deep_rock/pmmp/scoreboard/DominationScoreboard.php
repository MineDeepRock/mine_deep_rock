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
            new Score($slot, "====Domination====", 0, 0),
            new Score($slot, "Map:" . $game->getMap()->getName(), 1, 1),
        ];

        $index = count($scores);
        foreach ($game->getTeams() as $team) {
            $maxScoreAsStr = $game->getMaxScore()->getValue() ?? "";

            $scores[] = new Score($slot,
                $team->getTeamColorFormat() . $team->getName() . TextFormat::RESET . ":" . $team->getScore()->getValue() . "/" . $maxScoreAsStr,
                $index,
                $index);
            
            $index++;
        }

        $scores[] = new Score($slot, "====Flags====", $index, $index);
        $index++;

        foreach ($flags as $flag) {
            $gauge = $flag->getGauge();

            if ($gauge->isEmpty()) {
                $scores[] = new Score($slot, $flag->getName() . ":0", $index, $index);
            } else if ($gauge->isOccupied()) {
                $team = TeamGameSystem::getTeam($game->getId(), $gauge->getOccupyingTeamId());
                $scores[] = new Score($slot, $team->getTeamColorFormat() . $flag->getName() . ":" . $gauge->asInt(), $index, $index);
            } else if ($gauge->isOwned()) {
                $team = TeamGameSystem::getTeam($game->getId(), $gauge->getOwingTeamId());
                $scores[] = new Score($slot, $flag->getName() . ":" . $team->getTeamColorFormat() . $gauge->asInt(), $index, $index);
            }
            $index++;
        }

        return parent::__create($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge());
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