<?php


namespace game_system\interpreter;


use game_system\model\Coordinate;
use game_system\model\SpawnBeacon;
use game_system\pmmp\client\MedicineBoxClient;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

//コントローラーでは？
class SpawnBeaconInterpreter
{
    private $usersService;
    private $gameScoresService;
    private $scheduler;

    private $spawnBeacon;

    public function __construct(
        Player $owner,
        Coordinate $coordinate,
        UsersService $usersService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->gameScoresService = $gameScoresService;
        $this->scheduler = $scheduler;

        $user = $this->usersService->getUserData($owner->getName());

        $this->spawnBeacon = new SpawnBeacon($user->getName(), $user->getBelongTeamId(), $coordinate);
    }

    /**
     * @return SpawnBeacon
     */
    public function getSpawnBeacon(): SpawnBeacon {
        return $this->spawnBeacon;
    }

    public function stop():void{
        $this->spawnBeacon->setIsAvailable(false);
    }
}