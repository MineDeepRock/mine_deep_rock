<?php


namespace gun_system;


use Client;
use gun_system\pmmp\items\ItemGun;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class GunSystemClient extends Client
{
    public function tryShooting(Item $item, Player $player): void {
        if ($item instanceof ItemGun) {
            $item->shoot($player);
        }
    }

    public function sendDamageByShooting(Player $attacker, Entity $entity) {
        $weapon = $attacker->getInventory()->getItemInHand();
        if ($weapon instanceof ItemGun) {
            $entity->setHealth($entity->getHealth() - $weapon->getGunData()->getBulletDamage());
        }
    }

}