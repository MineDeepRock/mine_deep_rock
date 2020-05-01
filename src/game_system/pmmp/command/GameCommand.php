<?php


namespace game_system\pmmp\command;


use game_system\GameSystemListener;
use game_system\model\map\RealisticWWIBattlefieldExtended;
use game_system\pmmp\WorldController;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class GameCommand extends Command
{
    private $listener;

    private $scheduler;

    public function __construct(Plugin $owner, GameSystemListener $listener, TaskScheduler $scheduler) {
        parent::__construct("game", "", "");
        $this->setPermission("Game.Command");
        $this->listener = $listener;
        $this->scheduler = $scheduler;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (count($args) === 0) {
            $sender->sendMessage("/game [args]");
            return true;
        }
        $player = $sender->getServer()->getPlayer($sender->getName());
        $method = $args[0];
        if ($method === "world") {
            if (count($args) < 2) {
                $sender->sendMessage("/world [worldName]");
                return false;
            }
            $worldController = new WorldController();
            $worldController->teleport($player, $args[1]);
        } if ($method === "start") {
            if (!$player->isOp()) {
                $sender->sendMessage("権限がありません");
                return false;
            }
            $result = $this->startGame();
            if (!$result) {
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
            if (!$result) {
                $player->sendMessage("試合が開かれていません");
                return false;
            }

            $player->sendMessage("試合を終了させました");//TODO
        } else if ($method === "join") {
            $result = $this->join($player->getName());
            if (!$result) {
                $player->sendMessage("試合が開かれていないか、すでに参加しています");
                return false;
            }

            $onlinePlayers = Server::getInstance()->getOnlinePlayers();
            foreach ($onlinePlayers as $onlinePlayer)
                $onlinePlayer->sendMessage($player->getName() . "が試合に参加しました");

        } else if ($method === "quit" || $method === "hub") {
            $this->quit($player->getName());
            $player->sendMessage("試合から抜けました");
        }
        return true;
    }

    private function createTeamDeathMatch(): bool {
        return $this->listener->initGame(new RealisticWWIBattlefieldExtended());
    }

    public function startGame(): bool {
        return $this->listener->startGame();
    }

    private function closeGame(): bool {
        return $this->listener->closeGame();
    }

    private function join(string $playerName): bool {
        return $this->listener->joinGame($playerName);
    }

    private function quit(string $playerName): void {
        $this->listener->quitGame($playerName);
    }
}