<?php


namespace mine_deep_rock\pmmp\listener;


use mine_deep_rock\GameTypeList;
use mine_deep_rock\pmmp\event\PlayerResortedEvent;
use pocketmine\event\Listener;
use team_game_system\model\Score;
use team_game_system\TeamGameSystem;

class TDMListener implements Listener
{
    public function onPlayerResortedEvent(PlayerResortedEvent $event) {

        $player = $event->getPlayer();
        $playerData = TeamGameSystem::getPlayerData($player);
        if ($playerData->getGameId() === null) return;
        $game = TeamGameSystem::getGame($playerData->getGameId());
        if (!$game->getType()->equals(GameTypeList::TDM())) return;

        if (!$event->isByRescue()) {
            //TODO:２チームしか想定していない
            foreach ($game->getTeams() as $team) {
                if (!$team->getId()->equals($playerData->getTeamId())) {
                    TeamGameSystem::addScore($game->getId(), $team->getId(), new Score(1));
                }
            }
            return;
        }
    }
}