<?php


namespace game_system\pmmp\command;


use game_system\GameSystemClient;
use game_system\model\TeamDeathMatch;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class GameCommand extends Command
{
    private $client;

    public function __construct(Plugin $owner, GameSystemClient $client) {
        parent::__construct("game", "", "");
        $this->setPermission("Game.Command");
        $this->client = $client;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/game [args]");
            return true;
        }
        $player = $sender->getServer()->getPlayer($sender->getName());
        $method = $args[0];
        if ($method === "create") {
            if (!$player->isOp()) {
                $sender->sendMessage("権限がありません");
                return false;
            }
            if (count($args) < 2) {
                $sender->sendMessage("/game create [TeamDeathMatch]");
                return false;
            }
            $gameName = $args[1];
            switch ($gameName) {
                case "TeamDeathMatch":
                    $this->createTeamDeathMatch();
                    $targetPlayers = $player->getLevel()->getPlayers();
                    foreach ($targetPlayers as $targetPlayer)
                        $targetPlayer->sendMessage("TeamDeathMatchが開かれました。 /game joinで参加しましょう");
                    break;
            }
        } else if ($method === "close") {
            if (!$player->isOp()) {
                $sender->sendMessage("権限がありません");
                return false;
            }
            $result = $this->closeGame();
            if (!$result)
                $player->sendMessage("試合が開かれていません");

            $player->sendMessage("試合を終了させました");//TODO
        } else if ($method === "join") {
            $result = $this->join($player->getName());
            if (!$result)
                $player->sendMessage("試合が開かれていません");

            $player->sendMessage("試合に参加しました");
        } else if ($method === "quit" || $method === "hub") {
            $this->quit($player->getName());
            $player->sendMessage("試合から抜けました");
        }
        return true;
    }

    private function createTeamDeathMatch(): bool {
        return $this->client->createGame(new TeamDeathMatch());
    }

    private function closeGame(): bool {
        return $this->client->closeGame();
    }

    private function join(string $playerName): bool {
        return $this->client->joinGame($playerName);
    }

    private function quit(string $playerName): void {
        $this->client->quitGame($playerName);
    }
}