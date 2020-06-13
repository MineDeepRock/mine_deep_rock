<?php

namespace mine_deep_rock\pmmp\commands;

use mine_deep_rock\pmmp\entities\TeamDeathMatchNPC;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\plugin\Plugin;

class NPCCommand extends Command
{
    public function __construct() {
        parent::__construct("npc", "", "");
        $this->setPermission("NPC.Command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/npc [args]");
            return true;
        }
        $player = $sender->getServer()->getPlayer($sender->getName());
        if (!$player->isOp()) {
            $sender->sendMessage("権限がありません");
            return false;
        }
        $method = $args[0];
        if ($method === "spawn") {
            if ($args < 2) {
                $sender->sendMessage("/npc [spawn] [name]");
                return true;
            }

            $nbt = new CompoundTag('', [
                'Pos' => new ListTag('Pos', [
                    new DoubleTag('', $player->getX()),
                    new DoubleTag('', $player->getY() + 0.5),
                    new DoubleTag('', $player->getZ())
                ]),
                'Motion' => new ListTag('Motion', [
                    new DoubleTag('', 0),
                    new DoubleTag('', 0),
                    new DoubleTag('', 0)
                ]),
                'Rotation' => new ListTag('Rotation', [
                    new FloatTag("", $player->getYaw()),
                    new FloatTag("", 0)
                ]),
            ]);
            switch ($args[1]) {
                case "TeamDeathMatch":
                    $dealer = new TeamDeathMatchNPC($player->getLevel(), $nbt);
                    $dealer->spawnToAll();
                    break;
            }
        }
        return false;
    }
}