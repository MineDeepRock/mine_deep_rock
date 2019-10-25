<?php

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use team_system\command\TeamSystemCommand;

class Main extends PluginBase implements Listener
{
    function onEnable()
    {
        $this->getServer()->getCommandMap()->register("team", new teamsystemcommand($this));
    }
}