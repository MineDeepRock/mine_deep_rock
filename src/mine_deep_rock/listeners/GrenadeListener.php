<?php

namespace mine_deep_rock\listeners;


use grenade_system\pmmp\events\FragGrenadeExplodeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use team_system\TeamSystem;

class GrenadeListener implements Listener
{

    public function onExplodeGrenade(FragGrenadeExplodeEvent $event) {
        $ownerTeamId = TeamSystem::getPlayerData($event->getOwner()->getName())->getBelongTeamId();
        $victimTeamId = TeamSystem::getPlayerData($event->getVictim()->getName())->getBelongTeamId();
        if ($ownerTeamId === null || $victimTeamId === null) return;
        if ($ownerTeamId->equal($victimTeamId)) {
            if ($event->getDistance() <= 4) {
                $damage = 20;
            } else {
                $damage = 15 - $event->getDistance();
            }
            $pk = new EntityDamageByEntityEvent($event->getOwner(), $event->getVictim(), EntityDamageEvent::CAUSE_CONTACT, $damage);
            $pk->call();
        }
    }
}