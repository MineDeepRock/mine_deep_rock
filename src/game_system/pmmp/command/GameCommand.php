<?php


namespace game_system\pmmp\command;


use game_system\GameSystemClient;
use game_system\model\map\RealisticWWIBattlefieldExtended;
use game_system\model\TeamDeathMatch;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\TaskScheduler;

class GameCommand extends Command
{
    private $client;

    private $scheduler;

    public function __construct(Plugin $owner, GameSystemClient $client, TaskScheduler $scheduler) {
        parent::__construct("game", "", "");
        $this->setPermission("Game.Command");
        $this->client = $client;
        $this->scheduler = $scheduler;
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
                    $result = $this->createTeamDeathMatch();
                    if (!$result){
                        $player->sendMessage("すでに作成済みのゲームがあります");
                        return false;
                    }

                    $targetPlayers = $player->getLevel()->getPlayers();
                    foreach ($targetPlayers as $targetPlayer)
                        $targetPlayer->sendMessage("TeamDeathMatchが作成されました。 /game joinで参加しましょう");
                    break;
            }
        } else if ($method === "start") {
            if (!$player->isOp()) {
                $sender->sendMessage("権限がありません");
                return false;
            }
            $result = $this->startGame();
            if (!$result){
                $player->sendMessage("試合が作られていません");
                return false;
            }

            $player->sendMessage("試合を開始しました");
        } else if ($method === "close") {
            if (!$player->isOp()) {
                $sender->sendMessage("権限がありません");
                return false;
            }
            $result = $this->closeGame();
            if (!$result){
                $player->sendMessage("試合が開かれていません");
                return false;
            }

            $player->sendMessage("試合を終了させました");//TODO
        } else if ($method === "join") {
            $result = $this->join($player->getName());
            if (!$result){
                $player->sendMessage("試合が開かれていないか、すでに試合がはじまっています");
                return false;
            }

            $player->sendMessage("試合に参加しました");
        } else if ($method === "quit" || $method === "hub") {
            $this->quit($player->getName());
            $player->sendMessage("試合から抜けました");
        }
        return true;
    }

    private function createTeamDeathMatch(): bool {
        return $this->client->createGame(new TeamDeathMatch(new RealisticWWIBattlefieldExtended(),$this->scheduler));
    }

    public function startGame(): bool {
        return $this->client->startGame();
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