<?php


namespace mine_deep_rock\service;


use mine_deep_rock\GameTypeList;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\model\Score;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class CreateTDMService
{

    static function execute(?Score $maxScore = null, ?int $maxPlayersCount = null, ?int $timeLimit = null): void {
        $teams = [
            Team::asNew("Red", TextFormat::RED),
            Team::asNew("Blue", TextFormat::BLUE),
        ];
        $map = TeamGameSystem::selectMap("BrokenCity", $teams);
        $game = Game::asNew(GameTypeList::TDM(), $map, $teams, $maxScore, $maxPlayersCount, $timeLimit);

        TeamGameSystem::registerGame($game);
    }
}