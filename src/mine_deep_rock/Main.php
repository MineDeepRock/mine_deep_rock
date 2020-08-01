<?php

namespace mine_deep_rock;

use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\GunRecord;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\pmmp\event\UpdatedPlayerStatusEvent;
use mine_deep_rock\pmmp\listener\BoxListener;
use mine_deep_rock\pmmp\listener\GrenadeListener;
use mine_deep_rock\pmmp\listener\GunListener;
use mine_deep_rock\pmmp\listener\TDMListener;
use mine_deep_rock\pmmp\scoreboard\PlayerStatusScoreboard;
use mine_deep_rock\pmmp\slot_menu\SettingEquipmentsMenu;
use mine_deep_rock\store\MilitaryDepartmentsStore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use slot_menu_system\SlotMenuSystem;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        GunRecordDAO::init();
        PlayerStatusDAO::init();
        MilitaryDepartmentsStore::init();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new TDMListener($this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GunListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BoxListener($this->getServer(), $this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GrenadeListener(), $this);
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $player->setGamemode(Player::ADVENTURE);
        SlotMenuSystem::send($player, new SettingEquipmentsMenu($this->getScheduler()));
        PlayerStatusScoreboard::send($player);

        $playerName = $player->getName();

        if (!PlayerStatusDAO::isExist($playerName)) {
            PlayerStatusDAO::save(PlayerStatus::asNew($playerName));
        }

        if (!GunRecordDAO::isExist($playerName)) {
            GunRecordDAO::registerOwner($playerName);
            GunRecordDAO::add($playerName, GunRecord::asNew("M1907SL"));
            GunRecordDAO::add($playerName, GunRecord::asNew("MP18"));
            GunRecordDAO::add($playerName, GunRecord::asNew("Chauchat"));
            GunRecordDAO::add($playerName, GunRecord::asNew("SMLEMK3"));
            GunRecordDAO::add($playerName, GunRecord::asNew("Mle1903"));
        }

        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);
    }

    public function onUpdatedPlayerStatus(UpdatedPlayerStatusEvent $event): void {
        $status = $event->getPlayerStatus();
        $player = $this->getServer()->getPlayer($status->getName());
        if ($player->getLevel() !== null) {
            if ($player->getLevel()->getName() === "lobby") {
                PlayerStatusScoreboard::update($player);
            }
        }
    }
}