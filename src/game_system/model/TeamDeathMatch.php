<?php


namespace game_system\model;


use Closure;
use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\map\RealisticWWIBattlefieldExtended;
use game_system\model\map\TeamDeathMatchMap;
use game_system\pmmp\WorldController;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

date_default_timezone_set('Asia/Tokyo');

class TeamDeathMatch extends Game
{
    private $limitSecond;
    private $elapsedSecond;

    private $scheduler;

    private $redTeam;
    private $redTeamScore;
    private $redTeamSpawnPoints;

    private $blueTeam;
    private $blueTeamScore;
    private $blueTeamSpawnPoints;

    private $map;

    public function __construct(TeamDeathMatchMap $map, TaskScheduler $scheduler) {
        $this->limitSecond = 600;
        $this->elapsedSecond = 0;

        $this->scheduler = $scheduler;

        $this->redTeam = new Team();
        $this->redTeamScore = 0;
        $this->redTeamSpawnPoints = $map->getRedTeamSpawnPoints();

        $this->blueTeam = new Team();
        $this->blueTeamScore = 0;
        $this->blueTeamSpawnPoints = $map->getBlueTeamSpawnPoints();

        $this->map = $map;
        parent::__construct();
    }

    public function start(array $participants, Closure $onFinished): void {
        $this->isStarted = true;
        $worldController = new WorldController();

        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());

            if ($participant->getBelongTeamId()->equal($this->redTeam->getId())) {
                $player->setNameTag(TextFormat::RED . $participant->getName());
                $player->sendMessage(TextFormat::RED . "あなたは赤チームです");
            } else {
                $player->setNameTag(TextFormat::BLUE . $participant->getName());
                $player->sendMessage(TextFormat::BLUE . "あなたは青チームです");
            }


            $worldController->teleport($player, $this->map->getName());

            $player->getInventory()->setContents([]);
            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "gun give " . $participant->getName() . " " . $participant->getSelectedWeaponName());
            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "give " . $participant->getName() . " 364");

            $api = EasyScoreboardAPI::getInstance();
            $api->sendScoreboard($player, "sidebar", "TeamDeathMatch", false);
            $api->setScore($player, "sidebar", "RedTeamScore:", $this->redTeamScore, 2);
            $api->setScore($player, "sidebar", "BlueTeamScore:", $this->blueTeamScore, 3);

            $this->reSpawn($participant);
        }

        $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $tick) use ($onFinished): void {
            $this->elapsedSecond++;

            $players = Server::getInstance()->getLevelByName("RealisticWWIBattlefieldExtended")->getPlayers();

            $api = EasyScoreboardAPI::getInstance();
            foreach ($players as $player) {
                $api->setScore($player, "sidebar", "残り時間:", $this->limitSecond - $this->elapsedSecond, 1);
            }

            if ($this->elapsedSecond === $this->limitSecond) {
                $winTeam = $this->redTeamScore > $this->blueTeamScore ? $this->redTeam : $this->blueTeam;
                $onFinished($winTeam);
            }
        }), 20 * 1);
    }

    public function joinGameOnTheWay(User $user) {
        $worldController = new WorldController();

        $userName = $user->getName();
        $player = Server::getInstance()->getPlayer($userName);

        $worldController->teleport($player, RealisticWWIBattlefieldExtended::NAME);

        $player->getInventory()->setContents([]);
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "gun give " . $userName . " " . $user->getSelectedWeaponName());

        $api = EasyScoreboardAPI::getInstance();
        $api->sendScoreboard($player, "sidebar", "TeamDeathMatch", false);
        $api->setScore($player, "sidebar", "RedTeamScore:", $this->redTeamScore, 2);
        $api->setScore($player, "sidebar", "BlueTeamScore:", $this->blueTeamScore, 3);

        $this->reSpawn($user);
    }


    /**
     * @return Team
     */
    public function getRedTeam(): Team {
        return $this->redTeam;
    }

    /**
     * @return Team
     */
    public function getBlueTeam(): Team {
        return $this->blueTeam;
    }

    public function onKilledPlayer(TeamId $attackerTeamId, User $killedUser): void {
        $players = Server::getInstance()->getLevelByName("RealisticWWIBattlefieldExtended")->getPlayers();
        $api = EasyScoreboardAPI::getInstance();

        $killedPlayerTeamId = $attackerTeamId->equal($this->redTeam->getId()) ? $this->blueTeam->getId() : $this->redTeam->getId();
        if ($attackerTeamId->equal($this->redTeam->getId())) {
            $this->reSpawn($killedUser);
            $this->redTeamScore++;
            foreach ($players as $player) {
                $api->setScore($player, "sidebar", "RedTeamScore:", $this->redTeamScore, 2);
            }
        } else {
            $this->reSpawn($killedUser);
            $this->blueTeamScore++;
            foreach ($players as $player) {
                $api->setScore($player, "sidebar", "BlueTeamScore:", $this->blueTeamScore, 3);
            }
        }
    }

    private function reSpawn(User $user) {
        $player = Server::getInstance()->getPlayer($user->getName());
        $player->getInventory()->setContents([]);
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "gun give " . $user->getName() . " " . $user->getSelectedWeaponName());
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "give " . $user->getName() . " 364");
        $belongTeamId = $user->getBelongTeamId();

        if ($belongTeamId->equal($this->redTeam->getId())) {
            $pos= $this->redTeamSpawnPoints[rand(0, count($this->redTeamSpawnPoints) - 1)];
            $player->teleport($pos);
        } else {
            $pos = $this->blueTeamSpawnPoints[rand(0, count($this->redTeamSpawnPoints) - 1)];
            $player->teleport($pos);
        }
    }
}