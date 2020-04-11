<?php

namespace team_system\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use team_system\service\MemberService;
use team_system\service\TeamService;
use team_system\TeamSystemClient;


class TeamCommand extends Command
{

    private $client;

    public function __construct(Plugin $owner, TeamService $teamService, MemberService $playerService) {
        parent::__construct("team", "", "");
        $this->setPermission("Team.Command");

        $this->client = new TeamSystemClient($teamService,$playerService);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/team [args]");
            return true;
        }
        $senderName = $sender->getName();
        $method = $args[0];
        switch ($method) {
            case "create":
                $this->client->create($senderName, function(){});
                break;
            case "join":
                $leaderName = count($args) == 1 ? "" : $args[1];
                $this->client->join($senderName, $leaderName,function(){});
                break;
            case "quit":
                $this->client->quit($senderName, function(){});
                break;
            case "yield":
                $nextOwnerName = count($args) == 1 ? "" : $args[1];
                $this->client->yield($senderName, function(){}, $nextOwnerName);
                break;
        }

        return true;
    }

}