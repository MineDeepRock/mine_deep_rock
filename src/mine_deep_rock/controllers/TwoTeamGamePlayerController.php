<?php


namespace mine_deep_rock\controllers;


use gun_system\GunSystem;
use military_department_system\MilitaryDepartmentSystem;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use weapon_data_system\models\GunData;
use weapon_data_system\WeaponDataSystem;

class TwoTeamGamePlayerController
{
    static public function setEffects(Player $player): void {
        $playerData = MilitaryDepartmentSystem::getPlayerData($player->getName());
        foreach ($playerData->getMilitaryDepartment()->getEffects() as $effect) $player->addEffect($effect);
    }

    static public function setEquipments(Player $player): void {
        $playerData = MilitaryDepartmentSystem::getPlayerData($player->getName());
        /** @var GunData $mainGunData */
        $mainGunData = WeaponDataSystem::get($player->getName(), $playerData->getEquipMainGunName());
        /** @var GunData $subGunData */
        $subGunData = WeaponDataSystem::get($player->getName(), $playerData->getEquipSubGunName());
        $items = [
            GunSystem::getGun($player, $mainGunData->getName(), $mainGunData->getScopeName()),
            GunSystem::getGun($player, $subGunData->getName(), $subGunData->getScopeName()),
        ];
        foreach ($playerData->getMilitaryDepartment()->getCanEquipGadgetsType() as $gadgetType) {
            $items[] = $gadgetType->toItem();
        }
        $player->getInventory()->setContents($items);
        $player->getInventory()->setItem(8, ItemFactory::get(ItemIds::ARROW, 0, 1));
    }

    static public function removeCadaverEntity(Player $player): void {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $player->getName()) {
                    $entity->kill();
                }
            }
        }
    }

    static public function getCadaverEntity(Player $player): ?CadaverEntity {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $player->getName()) {
                    return $entity;
                }
            }
        }

        return null;
    }
}