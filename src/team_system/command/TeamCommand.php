<?php

namespace team_system\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use team_system\service\PlayerService;
use team_system\service\TeamService;
use team_system\TeamSystemClient;


class TeamCommand extends Command
{

    private $client;

    public function __construct(Plugin $owner, TeamService $teamService, PlayerService $playerService) {
        parent::__construct("team", "", "");
        $this->setPermission("Team.Command");

        $this->client = new TeamSystemClient($teamService,$playerService);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/team [args]");
            return true;
        }
        $playerName = $sender->getName();
        $method = $args[0];
        switch ($method) {
            case "create":
                $this->client->create($playerName, function(){});
                break;
            case "join":
                $ownerName = count($args) == 1 ? "" : $args[1];
                $this->client->join($playerName, $ownerName,function(){});
                break;
            case "quit":
                $this->client->quit($playerName, function(){});
                break;
            case "yield":
                $nextOwnerName = count($args) == 1 ? "" : $args[1];
                $this->client->yield($playerName, function(){}, $nextOwnerName);
                break;
        }

        return true;
    }

}