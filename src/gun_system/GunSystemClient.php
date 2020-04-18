<?php


namespace gun_system;


use Client;
use gun_system\pmmp\items\ItemGun;
use gun_system\pmmp\items\ItemSniperRifle;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;

class GunSystemClient extends Client
{
    public function tryShootingOnce(Item $item, Player $player): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $item->shootOnce($player);
        }
    }

    public function tryShooting(Item $item, Player $player): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            if ($item instanceof ItemSniperRifle) {
                $item->aim($player);
            } else {
                $item->shoot($player);
            }
        }
    }

    public function tryReloading(Item $item, Player $player): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $item->reload($player);
        }
    }

    public function sendDamageByShooting(Player $attacker, Entity $entity) {
        $weapon = $attacker->getInventory()->getItemInHand();
        if ($weapon instanceof ItemGun) {
            $entity->setHealth($entity->getHealth() - $weapon->getGunData()->getBulletDamage());
        }
    }

}