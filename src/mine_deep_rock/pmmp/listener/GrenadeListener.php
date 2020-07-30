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
        $ownerTeamId = TeamGameSystem::getPlayerData($event->getOwner())->getTeamId();
        $victimTeamId = TeamGameSystem::getPlayerData($event->getVictim())->getTeamId();
        if ($ownerTeamId === null || $victimTeamId === null) return;
        if ($ownerTeamId->equals($victimTeamId)) {
            if ($event->getDistance() <= 4) {
                $damage = 20;
            } else {
                $damage = 15 - $event->getDistance();
            }
            $pk = new EntityDamageByEntityEvent($event->getOwner(), $event->getVictim(), EntityDamageEvent::CAUSE_CONTACT, $damage);
            $pk->call();
        }
    }

    public function onExplodeFlameBottle(FlameBottleExplodeEvent $event) {
        $ownerTeamId = TeamGameSystem::getPlayerData($event->getOwner())->getTeamId();
        $victimTeamId = TeamGameSystem::getPlayerData($event->getVictim())->getTeamId();
        if ($ownerTeamId === null || $victimTeamId === null) return;
        if ($ownerTeamId->equals($victimTeamId)) {
            $pk = new EntityDamageByEntityEvent($event->getOwner(), $event->getVictim(), EntityDamageEvent::CAUSE_CONTACT, 4);
            $pk->call();
        }
    }
}