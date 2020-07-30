<?php

namespace mine_deep_rock;

use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\GunRecord;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\pmmp\listener\GunListener;
use mine_deep_rock\pmmp\listener\TDMListener;
use mine_deep_rock\store\MilitaryDepartmentsStore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        GunRecordDAO::init();
        PlayerStatusDAO::init();
        MilitaryDepartmentsStore::init();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new TDMListener($this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GunListener(), $this);
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $playerName = $player->getName();

        if (!PlayerStatusDAO::isExist($playerName)) {
            $militaryDepartment = MilitaryDepartmentsStore::get("AssaultSoldier");
            PlayerStatusDAO::save(new PlayerStatus($playerName, $militaryDepartment, $militaryDepartment->getDefaultGunName(), "Mle1903"));
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
}