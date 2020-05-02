<?php


namespace gun_system;


use Client;
use gun_system\pmmp\items\ItemGun;
use gun_system\pmmp\items\ItemSniperRifle;
use pocketmine\block\Redstone;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;

class GunSystemListener extends Client
{
    public function tryShootingOnce(Player $player, Item $item): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            if (!$player->getInventory()->contains(ItemFactory::get(Item::ARROW, 0, 1))) {
                $player->sendMessage("矢がないと銃を撃つことはできません");
            } else {
                $item->shootOnce();
            }
        }
    }

    public function tryShooting(Player $player, Item $item): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            if (!$player->getInventory()->contains(ItemFactory::get(Item::ARROW, 0, 1))) {
                $player->sendMessage("矢がないと銃を撃つことはできません");
            } else if (!($item instanceof ItemSniperRifle)) {
                $item->shoot();
            }
        }
    }

    public function tryReloading(Item $item): void {
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $item->reload();
        }
    }

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

            $entity->getLevel()->addParticle(new DestroyBlockParticle(new Vector3(
                $entity->getX(),
                $entity->getY() + 1,
                $entity->getZ()
            ), new Redstone()));

            $itemGun = $attacker->getInventory()->getItemInHand();
            if ($itemGun instanceof ItemGun) {
                $gun = $itemGun->getGunData();

                if (intval($distance) > 99) {
                    $damage = $gun->getBulletDamage()->getMaxDamage() * $gun->getEffectiveRange()[99] / 100;
                } else {
                    $damage = $gun->getBulletDamage()->getMaxDamage() * $gun->getEffectiveRange()[intval($distance)] / 100;
                }

                return $damage / 5;
            }
        }
        return 0;
    }

}