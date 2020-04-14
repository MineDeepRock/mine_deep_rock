<?php


namespace gun_system;


use Client;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class GunSystemClient extends Client
{
    public function tryShooting(Item $item, Player $player): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $item->shoot($player);
        }
    }

    public function sendDamageByShooting(Player $attacker, Entity $entity) {
        var_dump($entity->getId());
        $weapon = $attacker->getInventory()->getItemInHand();
        if (is_subclass_of($weapon, "gun_system\pmmp\items\ItemGun")) {
            $entity->setHealth($entity->getHealth() - $weapon->getGunData()->getAttackPower() );
        }
    }

}