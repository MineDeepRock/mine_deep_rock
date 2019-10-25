<?php

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

}