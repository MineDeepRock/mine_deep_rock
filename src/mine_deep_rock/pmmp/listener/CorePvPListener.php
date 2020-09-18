<?php


namespace mine_deep_rock\pmmp\listener;


use gun_system\pmmp\event\BulletHitBlockEvent;
use gun_system\service\CalculateDamageService;
use mine_deep_rock\store\CoresStore;
use team_game_system\pmmp\event\FinishedGameEvent;

class CorePvPListener
{
    public function onBulletHitCore(BulletHitBlockEvent $event) {
        foreach (CoresStore::getAll() as $core) {
            if ($core->getPosition()->equals($event->getBlock())) {
                $damage = CalculateDamageService::execute($event->getShooter(), $event->getBlock());
                $core->setHealth($core->getHealth() - $damage);
            }
        }
    }

    public function onFinishedGame(FinishedGameEvent $event) {
        CoresStore::delete($event->getGame()->getId());
    }
}