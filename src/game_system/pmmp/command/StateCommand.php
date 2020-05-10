<?php


namespace game_system\pmmp\command;

use game_system\listener\UsersListener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class StateCommand extends Command
{
    private $listener;

    public function __construct(Plugin $owner, UsersListener $listener) {
        parent::__construct("state", "", "");
        $this->setPermission("State.Command");
        $this->listener = $listener;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        $player = $sender->getServer()->getPlayer($sender->getName());
        $this->listener->showUserStatus($player);
        return true;
    }
}
