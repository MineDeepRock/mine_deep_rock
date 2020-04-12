<?php

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use team_system\command\TeamCommand;
use team_system\services\MemberService;
use team_system\services\TeamService;
use team_system\TeamSystemClient;

class Main extends PluginBase implements Listener
{
    private $teamSystemClient;

    function onEnable() {
        $this->teamSystemClient = new TeamSystemClient(new TeamService(), new MemberService());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("team", new TeamCommand($this, $this->teamService, $this->memberService));
    }

    public function onJoin(PlayerJoinEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->teamSystemClient->onJoin($playerName);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->teamSystemClient->onLeave($playerName);

    }
}