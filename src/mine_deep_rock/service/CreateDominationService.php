<?php


namespace mine_deep_rock\service;


use mine_deep_rock\dao\DominationFlagDataDAO;
use mine_deep_rock\GameTypeList;
use mine_deep_rock\model\DominationFlag;
use mine_deep_rock\store\DominationFlagsStore;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\model\Score;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class CreateDominationService
{

    static function execute(?Score $maxScore = null, ?int $maxPlayersCount = null, ?int $timeLimit = null): void {
        $teams = [
            Team::asNew("Red", TextFormat::RED),
            Team::asNew("Blue", TextFormat::BLUE),
        ];
        $map = TeamGameSystem::selectMap("BrokenCity", $teams);
        $game = Game::asNew(GameTypeList::Domination(), $map, $teams, $maxScore, $maxPlayersCount, $timeLimit);

        foreach (DominationFlagDataDAO::getFlagDataList($map->getName()) as $flagData) {
            $flag = DominationFlag::asNew($flagData->getName(), $game->getId(), $flagData->getPosition());
            DominationFlagsStore::add($flag);
        }

        TeamGameSystem::registerGame($game);
    }
}