<?php


namespace mine_deep_rock\pmmp\listener;


use mine_deep_rock\GameTypeList;
use mine_deep_rock\pmmp\scoreboard\DominationScoreboard;
use mine_deep_rock\service\OccupyFlagService;
use mine_deep_rock\store\DominationFlagsStore;
use pocketmine\Server;
use team_game_system\pmmp\event\UpdatedGameTimerEvent;
use team_game_system\TeamGameSystem;

class DominationListener
{
    public function onUpdatedTime(UpdatedGameTimerEvent $event): void {
        $gameId = $event->getGameId();
        $game = TeamGameSystem::getGame($gameId);
        if (!$game->getType()->equals(GameTypeList::Domination())) return;

        $flags = DominationFlagsStore::findByGameId($gameId);
        $levelPlayers = Server::getInstance()->getLevelByName($game->getMap()->getName())->getPlayers();


        foreach ($flags as $flag) {
            $aroundPlayersData = [];
            foreach ($levelPlayers as $player) {
                if (!$player->isOnline()) continue;
                if ($player->getPosition()->distance($flag->getPosition()) <= 10) {
                    $aroundPlayersData[] = TeamGameSystem::getPlayerData($player);
                }
            }

            OccupyFlagService::execute($flag, $aroundPlayersData);

            //TODO:$flagが更新されるか確認
            if ($flag->getGauge()->isOccupied()) {
                TeamGameSystem::addScore($flag->getGameId(), $flag->getGauge()->getOccupyingTeamId(), 1);
            }
        }

        $server = Server::getInstance();
        foreach (TeamGameSystem::getGamePlayersData($gameId) as $playerData) {
            $player = $server->getPlayer($playerData->getName());

            DominationScoreboard::update(
                $player,
                $game,
                DominationFlagsStore::findByGameId($gameId));
        }
    }
}