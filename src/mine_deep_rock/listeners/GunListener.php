<?php


namespace mine_deep_rock\listeners;


use gun_system\GunSystem;
use gun_system\pmmp\event\BulletHitEvent;
use gun_system\pmmp\event\BulletHitNearEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use team_system\TeamSystem;

class GunListener implements Listener
{
    public function onBulletHit(BulletHitEvent $event) {
        $pk = new EntityDamageByEntityEvent($event->getAttacker(), $event->getVictim(), EntityDamageEvent::CAUSE_CONTACT, $event->getDamage());
        $pk->call();
    }

    public function onBulletHitNear(BulletHitNearEvent $event) {
        $attacker = $event->getAttacker();
        $victim = $event->getVictim();

        $attackerData = TeamSystem::getPlayerData($attacker);
        $victimData = TeamSystem::getPlayerData($victim);
        if ($attackerData->getBelongTeamId() === null || $victimData->getBelongTeamId() === null) return;
        if ($attackerData->getBelongTeamId()->equal($victimData->getBelongTeamId())) {
            GunSystem::threaten($victim);
        }
    }
}