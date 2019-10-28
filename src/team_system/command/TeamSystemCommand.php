<?php

namespace team_system\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use team_system\models\Player;
use team_system\service\TeamService;


class TeamSystemCommand extends Command
{
    private $service;

    public function __construct(Plugin $owner)
    {
        parent::__construct("team", "", "");
        $this->setPermission("TeamSystem.Command");

        $this->service = new TeamService();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {

        if (count($args) == 0) {
            $sender->sendMessage("/team [args]");
            return true;
        }

        $player = new Player($sender->getName());
        $method = $args[0];
        switch ($method) {
            case "create":
                $this->service->create($player);
                break;
        }

        return true;
    }
}