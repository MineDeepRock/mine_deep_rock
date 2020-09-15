<?php


namespace mine_deep_rock\service;


use mine_deep_rock\GameTypeList;
use mine_deep_rock\model\OneOnOneRequest;
use mine_deep_rock\store\OneOnOneRequestsStore;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class AcceptOneOnOneRequestService
{
    static function execute(OneOnOneRequest $request): void {
        OneOnOneRequestsStore::delete($request);

        $teams = [
            Team::asNew($request->getOwnerName(), TextFormat::RED),
            Team::asNew($request->getReceiverName(), TextFormat::BLUE),
        ];
        $map = TeamGameSystem::selectMap($request->getMapName(), $teams);
        $game = Game::asNew(GameTypeList::TDM(), $map, $teams);
        $game->setMaxScore($request->getMaxScore());
        $game->setMaxPlayersCount(2);
        $game->setMaxPlayersDifference(1);
        $game->setTimeLimit($request->getTimeLimit());

        TeamGameSystem::registerGame($game);
    }
}