<?php


namespace mine_deep_rock\pmmp\listener;


use grenade_system\pmmp\events\FlameBottleExplodeEvent;
use grenade_system\pmmp\events\FragGrenadeExplodeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use team_game_system\TeamGameSystem;

class GrenadeListener implements Listener
{

    public function onExplodeGrenade(FragGrenadeExplodeEvent $event) {
        $owner = $event->getOwner();
        $victim = $event->getVictim();

        $ownerTeamId = TeamGameSystem::getPlayerData($owner)->getTeamId();
        $victimTeamId = TeamGameSystem::getPlayerData($victim)->getTeamId();
        if ($ownerTeamId === null || $victimTeamId === null) return;
        if ($ownerTeamId->equals($victimTeamId)) {
            if ($event->getDistance() <= 4) {
                $damage = 20;
            } else {
                $damage = 15 - $event->getDistance();
            }

            $victim->attack(new EntityDamageByEntityEvent($owner, $victim, EntityDamageEvent::CAUSE_CONTACT, $damage, [], 1));
        }
    }

    public function onExplodeFlameBottle(FlameBottleExplodeEvent $event) {
        $owner = $event->getOwner();
        $victim = $event->getVictim();

        $ownerTeamId = TeamGameSystem::getPlayerData($owner)->getTeamId();
        $victimTeamId = TeamGameSystem::getPlayerData($victim)->getTeamId();
        if ($ownerTeamId === null || $victimTeamId === null) return;
        if ($ownerTeamId->equals($victimTeamId)) {
            $victim->attack(new EntityDamageByEntityEvent($owner, $victim, EntityDamageEvent::CAUSE_CONTACT, 4, [], 0));
        }
    }
}