<?php


namespace mine_deep_rock\pmmp\service;


use box_system\pmmp\items\BoxItem;
use grenade_system\pmmp\items\GrenadeItem;
use gun_system\GunSystem;
use gun_system\model\attachment\Scope;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use pocketmine\item\Arrow;
use pocketmine\item\ChainHelmet;
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

class InitTDMEquipmentsPMMPService
{
    static function execute(Player $player): void {
        $player->getInventory()->setContents([]);
        $player->getArmorInventory()->setContents([]);

        $status = PlayerStatusDAO::get($player->getName());

        //銃のセット
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

        //箱のセット
        $boxes = $status->getMilitaryDepartment()->getBoxes();
        foreach ($boxes as $box) $inventory->addItem(BoxItem::fromBox($box));

        //グレネードのセット
        $grenades = $status->getMilitaryDepartment()->getGrenades();
        foreach ($grenades as $grenade) $inventory->addItem(GrenadeItem::fromGrenade($grenade));

        //矢のセット
        $inventory->setItem(8, new Arrow());

        //防具のセット
        //TODO:リファクタリング
        $playerData = TeamGameSystem::getPlayerData($player);
        $game = TeamGameSystem::getGame($playerData->getGameId());
        $teams = $game->getTeams();
        foreach ($teams as $team) {
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
}