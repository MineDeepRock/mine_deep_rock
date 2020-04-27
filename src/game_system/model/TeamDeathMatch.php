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

    public function start(Closure $onFinished): void {
        $this->isStarted = true;

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

    /**
     * @return TeamDeathMatchMap
     */
    public function getMap(): TeamDeathMatchMap {
        return $this->map;
    }

    /**
     * @param TeamId $userTeamId
     * @return Coordinate
     */
    public function getSpawnPoint(TeamId $userTeamId): Coordinate {
        if ($userTeamId->equal($this->redTeam->getId())) {
            return $this->redTeamSpawnPoints[rand(0, count($this->redTeamSpawnPoints) - 1)];;
        } else {
            return $this->blueTeamSpawnPoints[rand(0, count($this->blueTeamSpawnPoints) - 1)];;
        }
        return new Coordinate(0,0,0);
    }
}