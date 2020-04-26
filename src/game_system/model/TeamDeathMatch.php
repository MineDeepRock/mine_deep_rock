<?php


namespace game_system\model;


use Closure;
use game_system\model\map\TeamDeathMatchMap;
use game_system\pmmp\WorldController;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

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

            $worldController->teleport($player, $this->map->getName());

            $player->getInventory()->setContents([]);
            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "gun give " . $participant->getName() . " " . $participant->getSelectedWeaponName());

            $this->reSpawn($participant->getBelongTeamId(),$player);
        }

        $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $tick) use ($onFinished): void {
            $this->elapsedSecond++;
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

    public function onKilledPlayer(TeamId $attackerTeamId, Player $killedPlayer): void {
        $killedPlayerTeamId = $attackerTeamId->equal($this->redTeam->getId()) ? $this->blueTeam->getId() : $this->redTeam->getId();
        if ($attackerTeamId->equal($this->redTeam->getId())) {
            $this->reSpawn($killedPlayerTeamId,$killedPlayer);
            $this->redTeamScore++;
        } else {
            $this->reSpawn($killedPlayerTeamId,$killedPlayer);
            $this->blueTeamScore++;
        }
    }

    private function reSpawn(TeamId $belongTeamId, Player $player) {
        if ($belongTeamId->equal($this->redTeam->getId())) {
            $player->teleport($this->redTeamSpawnPoints[rand(0, count($this->redTeamSpawnPoints) - 1)]);
        } else {
            $player->teleport($this->blueTeamSpawnPoints[rand(0, count($this->redTeamSpawnPoints) - 1)]);
        }
    }
}