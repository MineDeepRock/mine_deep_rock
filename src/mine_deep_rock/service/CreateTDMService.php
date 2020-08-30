<?php


namespace mine_deep_rock\service;


use Exception;
use mine_deep_rock\GameTypeList;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\model\Score;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class CreateTDMService
{

    static function execute(string  $mapName,?Score $maxScore = null, ?int $maxPlayersCount = null, ?int $timeLimit = null): void {
        $teams = [
            Team::asNew("Red", TextFormat::RED),
            Team::asNew("Blue", TextFormat::BLUE),
        ];

        try {
            $map = TeamGameSystem::selectMap($mapName, $teams);

            $game = Game::asNew(GameTypeList::TDM(), $map, $teams, $maxScore, $maxPlayersCount, $timeLimit);
            TeamGameSystem::registerGame($game);
        } catch (Exception $e) {
            Server::getInstance()->getLogger()->info("{$mapName}は存在しません");
        }
    }
}