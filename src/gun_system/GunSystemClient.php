<?php


namespace gun_system;


use Client;
use gun_system\pmmp\items\ItemGun;
use gun_system\pmmp\items\ItemSniperRifle;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
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
        $item->reload($player);
    }

    public function sendDamageByShooting(?Player $attacker, Entity $entity) {
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

                $damage = $gun->getDamageCurve()[intval($distance)];

                $entity->setHealth($entity->getHealth() - $damage/5);//プレイヤーのHPが20なので
            }
        }
    }

}