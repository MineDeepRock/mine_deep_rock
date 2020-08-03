<?php


namespace mine_deep_rock\pmmp\service;


use box_system\pmmp\items\BoxItem;
use grenade_system\pmmp\items\GrenadeItem;
use gun_system\GunSystem;
use gun_system\model\attachment\Scope;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\item\Arrow;
use pocketmine\Player;

class InitTDMEquipmentsPMMPService
{
    static function execute(Player $player): void {
        $player->getInventory()->setContents([]);

        $status = PlayerStatusDAO::get($player->getName());

        $mainGunRecord = GunRecordDAO::get($player->getName(), $status->getMainGunName());
        $mainGun = GunSystem::findGunByName($mainGunRecord->getName());
        $mainGun->setScope(new Scope($mainGunRecord->getScopeMagnification()));

        $subGunRecord = GunRecordDAO::get($player->getName(), $status->getSubGunName());
        $subGun = GunSystem::findGunByName($subGunRecord->getName());
        $subGun->setScope(new Scope($subGunRecord->getScopeMagnification()));

        $inventory = $player->getInventory();
        $inventory->addItem(
            GunSystem::getItemGunFromGun($mainGun),
            GunSystem::getItemGunFromGun($subGun)
        );

        $boxes = $status->getMilitaryDepartment()->getBoxes();
        foreach ($boxes as $box) $inventory->addItem(BoxItem::fromBox($box));

        $grenades = $status->getMilitaryDepartment()->getGrenades();
        foreach ($grenades as $grenade) $inventory->addItem(GrenadeItem::fromGrenade($grenade));

        $inventory->setItem(8, new Arrow());
    }
}