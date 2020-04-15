<?php


namespace gun_system;


use Client;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GunSystemClient extends Client
{
    public function tryShooting(Item $item, Player $player, TaskScheduler $scheduler): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $item->shoot($player, $scheduler);
        }
    }

    public function sendDamageByShooting(Player $attacker, Entity $entity) {
        $weapon = $attacker->getInventory()->getItemInHand();
        if (is_subclass_of($weapon, "gun_system\pmmp\items\ItemGun")) {
            $entity->setHealth($entity->getHealth() - $weapon->getGunData()->getAttackPower());
        }
    }

}