<?php


namespace mine_deep_rock\pmmp\listener;


use grenade_system\pmmp\events\ConsumedGrenadeEvent;
use grenade_system\pmmp\events\FragGrenadeExplodeEvent;
use grenade_system\pmmp\items\GrenadeItem;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use team_game_system\TeamGameSystem;

class GrenadeListener implements Listener
{
    private $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
    }

    public function onExplodeGrenade(FragGrenadeExplodeEvent $event) {
        $owner = $event->getOwner();
        $victim = $event->getVictim();

        $ownerTeamId = TeamGameSystem::getPlayerData($owner)->getTeamId();
        $victimTeamId = TeamGameSystem::getPlayerData($victim)->getTeamId();
        if ($ownerTeamId === null || $victimTeamId === null) return;
        if (!$ownerTeamId->equals($victimTeamId)) {
            if ($event->getDistance() <= 4) {
                $damage = 20;
            } else {
                $damage = 20 - $event->getDistance();
            }

            $source = new EntityDamageByEntityEvent($owner, $victim, EntityDamageEvent::CAUSE_CONTACT, $damage, [], 0.4);
            $source->call();
            $victim->setLastDamageCause($source);

            $victim->setHealth($victim->getHealth() - $damage);
        }
    }

    public function onConsumedGrenade(ConsumedGrenadeEvent $event) {
        $grenade = $event->getGrenade();
        $owner = $event->getOwner();

        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($owner, $grenade): void {

            $playerData = TeamGameSystem::getPlayerData($owner);
            if ($playerData->getGameId() === null) return;

            $status = PlayerStatusDAO::get($owner->getName());
            $grenades = $status->getMilitaryDepartment()->getGrenades();
            if (in_array($grenade, $grenades)) {
                $grenadeItem = GrenadeItem::fromGrenade($grenade);
                if ($owner->getGamemode() === Player::SPECTATOR) return;

                if (!$owner->getInventory()->contains($grenadeItem)) {
                    $owner->getInventory()->addItem($grenadeItem);
                }
            }
        }), 20 * 10);
    }
}