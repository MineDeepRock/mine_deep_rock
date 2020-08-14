<?php


namespace mine_deep_rock\pmmp\listener;


use gun_system\GunSystem;
use gun_system\pmmp\event\BulletHitEvent;
use gun_system\pmmp\event\BulletHitNearEvent;
use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\pmmp\entity\TeamDeathMatchNPC;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use team_game_system\TeamGameSystem;

class GunListener implements Listener
{
    public function onBulletHit(BulletHitEvent $event) {
        $attacker = $event->getAttacker();
        $victim = $event->getVictim();

        if ($victim instanceof Player && $victim->getLevel()->getName() === "lobby") return;
        if ($victim instanceof TeamDeathMatchNPC) return;
        if ($victim instanceof CadaverEntity) return;

        if ($attacker instanceof Player && $victim instanceof Player) {
            $attackerData = TeamGameSystem::getPlayerData($attacker);
            $victimData = TeamGameSystem::getPlayerData($victim);
            if ($attackerData->getTeamId() === null || $victimData->getTeamId() === null) return;
            if (!$attackerData->getTeamId()->equals($victimData->getTeamId())) {
                $source = new EntityDamageByEntityEvent($attacker, $victim, EntityDamageEvent::CAUSE_CONTACT, $event->getDamage(), [], 0);
                $source->call();
                $victim->setLastDamageCause($source);

                $victim->setHealth($victim->getHealth() - $event->getDamage());
            }
        } else {
            $source = new EntityDamageByEntityEvent($attacker, $victim, EntityDamageEvent::CAUSE_CONTACT, $event->getDamage(), [], 0);
            $source->call();
            $victim->setLastDamageCause($source);

            $victim->setHealth($victim->getHealth() - $event->getDamage());
        }
    }

    public function onBulletHitNear(BulletHitNearEvent $event) {
        $attacker = $event->getAttacker();
        $victim = $event->getVictim();

        $attackerData = TeamGameSystem::getPlayerData($attacker);
        $victimData = TeamGameSystem::getPlayerData($victim);
        if ($attackerData->getTeamId() === null || $victimData->getTeamId() === null) return;
        if (!$attackerData->getTeamId()->equals($victimData->getTeamId())) {
            GunSystem::giveScare($victim);
        }
    }
}