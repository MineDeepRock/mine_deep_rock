<?php


namespace game_system\interpreter;


use game_system\model\Coordinate;
use game_system\model\MedicineBox;
use game_system\model\military_department\NursingSoldier;
use game_system\pmmp\client\MedicineBoxClient;
use game_system\pmmp\items\SpawnMedicineBoxItem;
use game_system\service\GameScoresService;
use game_system\service\UsersService;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;

class MedicineBoxInterpreter
{
    private $client;
    private $usersService;
    private $gameScoresService;
    private $scheduler;
    private $handler;

    private $owner;
    private $ownerTeamId;
    private $medicineBox;


    function __construct(
        Player $player,
        Coordinate $coordinate,
        UsersService $usersService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->gameScoresService = $gameScoresService;
        $this->client = new MedicineBoxClient();
        $this->scheduler = $scheduler;

        $this->medicineBox = new MedicineBox($coordinate);
        $this->owner = $player;
        $user = $this->usersService->getUserData($player->getName());
        $gameId = $user->getParticipatedGameId();
        $this->ownerTeamId = $user->getBelongTeamId();

        $this->handler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $tick) use ($gameId): void {
            if (!$this->owner->isOnline()) {
                $this->stop();
            } else {
                $this->client->summonParticle(
                    $this->owner->getLevel(),
                    new Vector3(
                        $this->medicineBox->getCoordinate()->getX(),
                        $this->medicineBox->getCoordinate()->getY(),
                        $this->medicineBox->getCoordinate()->getZ()));
                foreach ($this->getAroundTeamPlayers() as $player) {
                    $this->gameScoresService->addPoint($this->owner->getName(),$gameId,2);
                    $this->client->useMedicineBox($this->owner, $player);
                }
            }
        }), 20 * 2, 20 * 5);
    }

    public function stop(): void {
        $this->handler->cancel();
        $this->giveAgain();
    }

    public function getMedicineBox(): MedicineBox {
        return $this->medicineBox;
    }

    private function getAroundTeamPlayers(): array {
        if ($this->owner->getLevel() === null) {
            return [];
        }
        $players = $this->owner->getLevel()->getPlayers();
        return array_filter($players, function ($player) {
            $belongTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
            if ($this->ownerTeamId === null) return false;
            if ($belongTeamId === null) return false;
            if (!$this->ownerTeamId->equal($belongTeamId)) return false;
            $ammoPosition = new Vector3(
                $this->medicineBox->getCoordinate()->getX(),
                $this->medicineBox->getCoordinate()->getY(),
                $this->medicineBox->getCoordinate()->getZ()
            );

            return $ammoPosition->distance($player->getPosition()) < 6;
        });
    }

    public function giveAgain(): void {
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick): void {
            if (!$this->owner->isOnline()) return;
            if ($this->owner->getGamemode() !== Player::ADVENTURE) return;

            $user = $this->usersService->getUserData($this->owner->getName());
            $nursingSoldier = new NursingSoldier();
            if ($user->getMilitaryDepartment()->getName() !== $nursingSoldier->getName()) return;

            $contain = $this->owner->getInventory()->contains(new SpawnMedicineBoxItem());
            if ($contain) return;

            $this->owner->getInventory()->addItem(new SpawnMedicineBoxItem());
        }), 20 * 10);
    }
}