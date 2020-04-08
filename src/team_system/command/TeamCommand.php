<?php

namespace team_system\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use team_system\models\Player;
use team_system\service\PlayerService;
use team_system\service\TeamService;


class TeamCommand extends Command
{
    private $teamService;
    private $playerService;

    public function __construct(Plugin $owner, TeamService $teamService, PlayerService $playerService) {
        parent::__construct("team", "", "");
        $this->setPermission("Team.Command");

        $this->teamService = $teamService;
        $this->playerService = $playerService;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/team [args]");
            return true;
        }

        $player = $this->playerService->getData($sender->getName());
        $method = $args[0];
        //TODO:リファクタリング
        switch ($method) {
            case "create":
                $this->create($player, $sender);
                break;
            case "join":
                $ownerName = count($args) == 1 ? "" : $args[1];
                $this->join($player, $sender, $ownerName);
                break;
            case "quit":
                $this->quit($player, $sender);
                break;
        }

        return true;
    }

    /**
     * @param Player $player
     * @param CommandSender $sender
     */
    private function create(Player $player, CommandSender $sender): void {
        $result = $this->teamService->create($player);
        if ($result->isSucceed()) {
            $this->playerService->updateBelongTeamId($player->getName(), $result->getValue()->getId());
            $message = "チームを作成しました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $sender->sendMessage($message);
    }

    /**
     * @param Player $player
     * @param CommandSender $sender
     * @param string $ownerName
     */
    private function join(Player $player, CommandSender $sender, string $ownerName): void {
        $result = $this->teamService->join($player, $ownerName);
        if ($result->isSucceed()) {
            $this->playerService->updateBelongTeamId($player->getName(), $result->getValue()->getId);
            $message = "チームに参加しました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $sender->sendMessage($message);
    }

    /**
     * @param Player $player
     * @param CommandSender $sender
     */
    private function quit(Player $player, CommandSender $sender): void {
        $result = $this->teamService->quit($player, $player->getBelongTeamId()->value());
        if ($result->isSucceed()) {
            $this->playerService->updateBelongTeamId($player->getName(), $result->getValue()->getId);
            $message = "チームを抜けました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $sender->sendMessage($message);
    }
}