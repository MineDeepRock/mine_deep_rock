<?php

namespace mine_deep_rock;

use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\light_machine_gun\Chauchat;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sub_machine_gun\MP18;
use mine_deep_rock\listeners\BoxListener;
use mine_deep_rock\listeners\GrenadeListener;
use mine_deep_rock\listeners\GunListener;
use mine_deep_rock\listeners\TwoTeamGameListener;
use mine_deep_rock\pmmp\commands\NPCCommand;
use mine_deep_rock\pmmp\entities\TeamDeathMatchNPC;
use mine_deep_rock\slot_menus\EquipmentSelectMenu;
use money_system\MoneySystem;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\plugin\PluginBase;
use slot_menu_system\SlotMenuSystem;
use weapon_data_system\models\GunData;
use weapon_data_system\WeaponDataSystem;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        Entity::registerEntity(TeamDeathMatchNPC::class, true, ['TeamDeathMatch']);

        $this->getServer()->getCommandMap()->register("npc", new NPCCommand());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new TwoTeamGameListener($this->getServer(),$this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GrenadeListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BoxListener($this->getServer(),$this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GunListener(), $this);
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);

        if (!WeaponDataSystem::isExist($playerName)) {
            WeaponDataSystem::init($playerName);
            WeaponDataSystem::add($playerName, new GunData(M1907SL::NAME, 0));
            WeaponDataSystem::add($playerName, new GunData(MP18::NAME, 0));
            WeaponDataSystem::add($playerName, new GunData(Chauchat::NAME, 0));
            WeaponDataSystem::add($playerName, new GunData(SMLEMK3::NAME, 0));
            WeaponDataSystem::add($playerName, new GunData(Mle1903::NAME, 0));
        }
        if (!MoneySystem::isExist($playerName)) {
            MoneySystem::register($playerName);
            MoneySystem::increase($playerName, 5000);
        }

        SlotMenuSystem::send($player, new EquipmentSelectMenu());
    }
}