<?php

namespace team_system\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use team_system\models\Player;
use team_system\service\TeamService;


class TeamCommand extends Command
{
    private $service;

    public function __construct(Plugin $owner) {
        parent::__construct("team", "", "");
        $this->setPermission("Team.Command");

        $this->service = new TeamService();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) == 0) {
            $sender->sendMessage("/team [args]");
            return true;
        }

        //TODO:PlayerデータはonJoin時に設定する。
        //こいつのせいで今ほとんどうまくいきません
        $player = new Player($sender->getName());
        $method = $args[0];
        switch ($method) {
            case "create":
                $result = $this->service->create($player);
                $message = $result->isSucceed()? "チームを作成しました" : $result->getValue()->getMessage();
                $sender->sendMessage($message);
                break;
            case "join":
                $ownerName = $method = $args[1];
                $result = $this->service->join($player,$ownerName);
                $message = $result->isSucceed()? "チームに参加しました" : $result->getValue()->getMessage();
                $sender->sendMessage($message);
                break;
            case "quit":
                $ownerName = $method = $args[1];
                $result = $this->service->quit($player,$ownerName);
                $message = $result->isSucceed()? "チームを抜けました" : $result->getValue()->getMessage();
                $sender->sendMessage($message);
                break;
        }

        return true;
    }
}