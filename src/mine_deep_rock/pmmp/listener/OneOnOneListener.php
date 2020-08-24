<?php


namespace mine_deep_rock\pmmp\listener;


use mine_deep_rock\GameTypeList;
use pocketmine\event\Listener;
use team_game_system\model\Score;
use team_game_system\pmmp\event\PlayerKilledPlayerEvent;
use team_game_system\TeamGameSystem;

class OneOnOneListener implements Listener
{
    public function onPlayerKilledPlayer(PlayerKilledPlayerEvent $event): void {
        $attacker = $event->getAttacker();
        $attackerData = TeamGameSystem::getPlayerData($attacker);
        $game = TeamGameSystem::getGame($attackerData->getGameId());

        if (!$game->getType()->equals(GameTypeList::OneOnOne())) return;
        TeamGameSystem::addScore($game->getId(), $attackerData->getTeamId(), new Score(1));
    }
}