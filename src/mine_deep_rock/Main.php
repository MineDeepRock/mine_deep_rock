<?php

namespace mine_deep_rock;

use mine_deep_rock\pmmp\listener\TDMListener;
use mine_deep_rock\store\MilitaryDepartmentsStore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        MilitaryDepartmentsStore::init();
        $this->getServer()->getPluginManager()->registerEvents(new TDMListener($this->getScheduler()), $this);
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);
    }
}