<?php


namespace mine_deep_rock\pmmp\listener;


use gun_system\GunSystem;
use gun_system\pmmp\event\BulletHitEvent;
use gun_system\pmmp\event\BulletHitNearEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use team_game_system\TeamGameSystem;

class GunListener implements Listener
{
    public function onBulletHit(BulletHitEvent $event) {
        $event->getVictim()->attack(new EntityDamageByEntityEvent($event->getAttacker(), $event->getVictim(), EntityDamageEvent::CAUSE_CONTACT, $event->getDamage(), [], 0));
    }

    public function onBulletHitNear(BulletHitNearEvent $event) {
        $attacker = $event->getAttacker();
        $victim = $event->getVictim();

        $attackerData = TeamGameSystem::getPlayerData($attacker);
        $victimData = TeamGameSystem::getPlayerData($victim);
        if ($attackerData->getTeamId() === null || $victimData->getTeamId() === null) return;
        if ($attackerData->getTeamId()->equals($victimData->getTeamId())) {
            GunSystem::giveScare($victim);
        }
    }
}