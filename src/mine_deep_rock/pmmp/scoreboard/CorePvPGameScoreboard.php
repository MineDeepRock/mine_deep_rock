<?php


namespace mine_deep_rock\pmmp\scoreboard;


use mine_deep_rock\store\CoresStore;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\Score;
use scoreboard_builder\Scoreboard;
use scoreboard_builder\ScoreboardSlot;
use scoreboard_builder\ScoreSortType;
use team_game_system\model\Game;

class CorePvPGameScoreboard extends Scoreboard
{
    private static function create(Game $game): Scoreboard {
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score("----Core----"),
            new Score(TextFormat::YELLOW . "Map:"),
            new Score(TextFormat::BOLD . "> " . $game->getMap()->getName()),
            new Score(TextFormat::LIGHT_PURPLE . ""),
            new Score(TextFormat::LIGHT_PURPLE . "Core HP:"),
        ];

        foreach ($game->getTeams() as $team) {
            $core = CoresStore::findByTeamId($team->getId());
            $scores[] = new Score($team->getTeamColorFormat() . " > {$team->getName()}:" . $core->getHealth());
        }

        $scores[] = new Score($slot, "------------------");

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