<?php


namespace gun_system;


use Client;
use gun_system\models\HandGun;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class GunSystemClient extends Client
{
    public function tryShooting(Item $item, Player $player): void {
        Item::STICK;
        switch ($item->getName()) {
            case "HandGun":
                $item->shoot($player);
        }
    }

    public function damageByShooting(Player $attacker,EntityDamageEvent $event){
        $weapon = $attacker->getInventory()->getItemInHand();
        switch ($weapon->getId()){
            case HandGun::getId();
                $event->setBaseDamage($weapon->getGunData()->getAttackPower());
        }
    }

}