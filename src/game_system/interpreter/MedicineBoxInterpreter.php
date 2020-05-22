<?php


namespace game_system\interpreter;


use game_system\model\Coordinate;
use game_system\model\MedicineBox;
use game_system\model\military_department\NursingSoldier;
use game_system\pmmp\client\MedicineBoxClient;
use game_system\pmmp\Entity\MedicineBoxEntity;
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
    private $gameId;


    function __construct(
        Player $player,
        UsersService $usersService,
        GameScoresService $gameScoresService,
        TaskScheduler $scheduler) {
        $this->usersService = $usersService;
        $this->gameScoresService = $gameScoresService;
        $this->client = new MedicineBoxClient();
        $this->scheduler = $scheduler;

        $this->owner = $player;
        $user = $this->usersService->getUserData($player->getName());
        $this->gameId = $user->getParticipatedGameId();
        $this->ownerTeamId = $user->getBelongTeamId();
    }

    public function carryOut(MedicineBoxEntity $medicineBoxEntity): void {
        $this->handler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(function (int $tick) use ($medicineBoxEntity): void {
            if (!$this->owner->isOnline()) {
                $this->stop();
            } else {
                $this->client->summonParticle(
                    $this->owner->getLevel(),
                    $medicineBoxEntity->getPosition());
                foreach ($this->getAroundTeamPlayers($medicineBoxEntity->getPosition()) as $player) {
                    $this->gameScoresService->addPoint($this->owner->getName(), $this->gameId, 2);
                    $this->client->useMedicineBox($this->owner, $player);
                }
            }
        }), 20 * 2, 20 * 5);
    }

    public function stop(): void {
        $this->handler->cancel();
        $this->giveAgain();
    }

    private function getAroundTeamPlayers(Vector3 $pos): array {
        if ($this->owner->getLevel() === null) {
            return [];
        }
        $players = $this->owner->getLevel()->getPlayers();
        return array_filter($players, function ($player) use($pos) {
            $belongTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
            if ($this->ownerTeamId === null) return false;
            if ($belongTeamId === null) return false;
            if (!$this->ownerTeamId->equal($belongTeamId)) return false;

            return $pos->distance($player->getPosition()) < 6;
        });
    }

    private function giveAgain(): void {
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