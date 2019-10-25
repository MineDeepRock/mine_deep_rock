<?php

namespace team_system\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class TeamSystemCommand extends Command
{
    public function __construct(Plugin $owner)
    {
        parent::__construct("team", "", "");
        $this->setPermission("TeamSystem.Command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        $sender->sendMessage("Command");
            return true;
        }
}