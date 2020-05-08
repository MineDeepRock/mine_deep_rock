<?php


namespace game_system\pmmp\command;


use game_system\GameSystemListener;
use game_system\pmmp\Entity\GunDealerNPC;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class NPCCommand extends Command
{
    private $listener;

    public function __construct(Plugin $owner) {
        parent::__construct("npc", "", "");
        $this->setPermission("NPC.Command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/npc [args]");
            return true;
        }
        $player = $sender->getServer()->getPlayer($sender->getName());
        $method = $args[0];
        if ($method === "spawn") {
            if ($args < 2) {
                $sender->sendMessage("/npc [spawn] [name]");
                return true;
            }

            switch ($args[1]) {
                case "GunDealer":
                    $dealer = new GunDealerNPC($player->getLevel(),$player);
                    $dealer->spawnToAll();
                    break;
            }
        }
        return false;
    }
}