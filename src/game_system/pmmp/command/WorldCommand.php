<?php


namespace game_system\pmmp\command;


use game_system\pmmp\WorldController;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class WorldCommand extends Command
{

    private $controller;

    public function __construct(Plugin $owner) {
        parent::__construct("world", "", "");
        $this->setPermission("World.Command");
        $this->controller = new WorldController();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/world [args]");
            return true;
        }
        $player = $sender->getServer()->getPlayer($sender->getName());
        $method = $args[0];
        if ($method === "tp") {
            if (!$player->isOp()) {
                $sender->sendMessage("権限がありません");
                return false;
            }
            if (count($args) < 2) {
                $sender->sendMessage("/world tp [worldName]");
                return true;
            }
            $this->controller->teleport($player,$args[1]);
        }
        return false;
    }
}
