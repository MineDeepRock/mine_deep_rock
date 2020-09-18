<?php


namespace mine_deep_rock\pmmp\listener;


use mine_deep_rock\store\CoresStore;
use team_game_system\pmmp\event\FinishedGameEvent;

class CorePvPListener
{
    public function onFinishedGame(FinishedGameEvent $event) {
        CoresStore::delete($event->getGame()->getId());
    }
}