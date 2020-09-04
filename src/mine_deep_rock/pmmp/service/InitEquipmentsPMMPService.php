<?php


namespace mine_deep_rock\pmmp\service;


use box_system\pmmp\items\BoxItem;
use grenade_system\pmmp\items\GrenadeItem;
use gun_system\GunSystem;
use gun_system\model\attachment\Scope;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\item\Arrow;
use pocketmine\item\GoldBoots;
use pocketmine\item\GoldChestplate;
use pocketmine\item\GoldHelmet;
use pocketmine\item\GoldLeggings;
use pocketmine\item\IronBoots;
use pocketmine\item\IronChestplate;
use pocketmine\item\IronHelmet;
use pocketmine\item\IronLeggings;
use pocketmine\Player;
use team_game_system\TeamGameSystem;

class InitEquipmentsPMMPService
{
    static function execute(Player $player): void {
        $player->getInventory()->setContents([]);
        $player->getArmorInventory()->setContents([]);

        $equipments = PlayerEquipmentsDAO::get($player->getName());

        //銃のセット
        $mainGunRecord = GunRecordDAO::get($player->getName(), $equipments->getMainGunName());
        $mainGun = GunSystem::findGunByName($mainGunRecord->getName());
        $mainGun->setScope(new Scope($mainGunRecord->getScopeMagnification()));

        $subGunRecord = GunRecordDAO::get($player->getName(), $equipments->getSubGunName());
        $subGun = GunSystem::findGunByName($subGunRecord->getName());
        $subGun->setScope(new Scope($subGunRecord->getScopeMagnification()));

        $inventory = $player->getInventory();
        $inventory->addItem(
            GunSystem::getItemGunFromGun($mainGun),
            GunSystem::getItemGunFromGun($subGun)
        );

        //箱のセット
        $boxes = $equipments->getMilitaryDepartment()->getBoxes();
        foreach ($boxes as $box) $inventory->addItem(BoxItem::fromBox($box));

        //グレネードのセット
        $grenades = $equipments->getMilitaryDepartment()->getGrenades();
        foreach ($grenades as $grenade) $inventory->addItem(GrenadeItem::fromGrenade($grenade));

        //矢のセット
        $inventory->setItem(8, new Arrow());

        //防具のセット
        //TODO:リファクタリング ２チームしか想定してない
        $playerData = TeamGameSystem::getPlayerData($player);
        $team = TeamGameSystem::getTeam($playerData->getGameId(), $playerData->getTeamId());
        if ($team->getId()->equals($playerData->getTeamId())) {
            $armorInventory = $player->getArmorInventory();
            if ($team->getName() === "Red") {
                $armorInventory->setHelmet(new GoldHelmet());
                $armorInventory->setChestplate(new GoldChestplate());
                $armorInventory->setLeggings(new GoldLeggings());
                $armorInventory->setBoots(new GoldBoots());
            } else {
                $armorInventory->setHelmet(new IronHelmet());
                $armorInventory->setChestplate(new IronChestplate());
                $armorInventory->setLeggings(new IronLeggings());
                $armorInventory->setBoots(new IronBoots());
            }
        }
    }
}