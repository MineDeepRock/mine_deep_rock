<?php


namespace gun_system;


use Client;
use gun_system\pmmp\items\ItemGun;
use gun_system\pmmp\items\ItemSniperRifle;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class GunSystemClient extends Client
{
    public function tryShootingOnce(Item $item): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $item->shootOnce();
        }
    }

    public function tryShooting(Item $item): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            if ($item instanceof ItemSniperRifle) {
                $item->aim();
            } else {
                $item->shoot();
            }
        }
    }

    public function tryReloading(Item $item): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $item->reload();
        }
    }

    //TODO:ダメージの計算はGameSystemでやる
    public function receivedDamage(?Player $attacker, Entity $entity): int {
        if ($attacker !== null) {

            $attackerPos = new Vector3(
                $attacker->getX(),
                $attacker->getY(),
                $attacker->getZ()
            );
            $entityPo = new Vector3(
                $entity->getX(),
                $entity->getY(),
                $entity->getZ()
            );

            $distance = $attackerPos->distance($entityPo);

            $itemGun = $attacker->getInventory()->getItemInHand();
            if ($itemGun instanceof ItemGun) {
                $gun = $itemGun->getGunData();

                if (intval($distance) > 99) {
                    $damage = end($gun->getDamageCurve());
                } else {
                    $damage = $gun->getDamageCurve()[intval($distance)];
                }

                return $damage/5;
            }
        }
        return 0;
    }

}