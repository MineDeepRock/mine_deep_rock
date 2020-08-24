<?php


namespace mine_deep_rock\pmmp\listener;


use mine_deep_rock\GameTypeList;
use mine_deep_rock\model\DominationFlag;
use mine_deep_rock\pmmp\scoreboard\DominationScoreboard;
use mine_deep_rock\pmmp\service\SummonFlagParticlePMMPService;
use mine_deep_rock\service\OccupyFlagService;
use mine_deep_rock\store\DominationFlagsStore;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use team_game_system\model\Score;
use team_game_system\pmmp\event\UpdatedGameTimerEvent;
use team_game_system\TeamGameSystem;

class DominationListener implements Listener
{
    public function onUpdatedTime(UpdatedGameTimerEvent $event): void {
        $gameId = $event->getGameId();
        $game = TeamGameSystem::getGame($gameId);
        $level = Server::getInstance()->getLevelByName($game->getMap()->getLevelName());
        if (!$game->getType()->equals(GameTypeList::Domination())) return;

        $flags = DominationFlagsStore::findByGameId($gameId);
        $levelPlayers = $level->getPlayers();


        foreach ($flags as $flag) {
            $aroundPlayersData = [];
            foreach ($levelPlayers as $player) {
                if (!$player->isOnline()) continue;
                if ($player->getGamemode() !== Player::ADVENTURE) continue;
                if ($player->getPosition()->distance($flag->getPosition()) <= DominationFlag::Range) {
                    $aroundPlayersData[] = TeamGameSystem::getPlayerData($player);
                }
            }

            OccupyFlagService::execute($flag, $aroundPlayersData);

            //TODO:$flagが更新されるか確認
            if ($flag->getGauge()->isOccupied()) {
                TeamGameSystem::addScore($flag->getGameId(), $flag->getGauge()->getOccupyingTeamId(), new Score(1));
            }

            SummonFlagParticlePMMPService::execute($flag, $level);
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