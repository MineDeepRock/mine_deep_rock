<?php

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use team_system\command\TeamCommand;
use team_system\services\MemberService;
use team_system\services\TeamService;

class Main extends PluginBase implements Listener
{
    private $playerService;
    private $teamService;

    function onEnable() {
        $this->playerService = new MemberService();
        $this->teamService = new TeamService();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("team", new TeamCommand($this,$this->teamService,$this->playerService));
    }

    public function onJoin(PlayerJoinEvent $event) {
        $playerName = $event->getPlayer()->getName();
        $this->playerService->init($playerName);

    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();
        $player = $this->playerService->login($playerName);
        $belongTeamId = $player->getBelongTeamId() == null ? "" : $player->getBelongTeamId()->value();

        $this->teamService->quit($player,$belongTeamId);

    }
}