<?php

namespace mine_deep_rock\pmmp\entity;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\DataFolderPath;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\pmmp\service\RescuePlayerPMMPService;
use mine_deep_rock\store\MilitaryDepartmentsStore;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\UUID;
use team_game_system\TeamGameSystem;

class CadaverEntity extends Human
{
    const NAME = "Cadaver";
    public $width = 0.6;
    public $height = 0.2;

    public $geometryId = "geometry." . self::NAME;
    public $geometryName = self::NAME . ".geo.json";

    private $owner;

    private const RescueRange = 2;
    private const MaxRescueGauge = 5;

    /**
     * @var Player
     */
    private $rescuingPlayer;
    /**
     * @var int
     */
    private $rescueGauge = 0;


    /**
     * @var TaskScheduler
     */
    private $scheduler;
    /**
     * @var TaskHandler
     */
    private $limitTaskHandler;
    /**
     * @var TaskHandler
     */
    private $rescueTaskHandler;

    public function __construct(Level $level, Player $owner, TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
        $this->owner = $owner;
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $owner->getX()),
                new DoubleTag('', $owner->getY()),
                new DoubleTag('', $owner->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $owner->getYaw()),
                new FloatTag("", 0)
            ]),
        ]);
        $this->uuid = UUID::fromRandom();
        $this->initSkin($owner);

        parent::__construct($level, $nbt);
        $this->setRotation($this->yaw, $this->pitch);
        $this->setNameTagAlwaysVisible(false);
        $this->sendSkin();
    }

    private function initSkin(Player $player): void {
        $this->setSkin(new Skin(
            "Standard_CustomSlim",
            $player->getSkin()->getSkinData(),
            "",
            $this->geometryId,
            file_get_contents(DataFolderPath::Geometry . $this->geometryName)
        ));
    }

    public function spawnToAll(): void {
        parent::spawnToAll();
        $ownerGameStatus = PlayerGameStatusStore::findByName($this->owner->getName());
        if ($ownerGameStatus->isResuscitated()) return;

        $this->limitTaskHandler = $this->scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick): void {
                if ($this->isAlive()) $this->kill();
            }
        ), 20 * 30);


        $this->rescueTaskHandler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(
            function (int $currentTick): void {
                if ($this->rescuingPlayer === null) {
                    $this->rescueGauge = 0;
                    $this->findRescuingPlayer();

                } else if (!$this->rescuingPlayer->isOnline()) {
                    $this->rescueGauge = 0;
                    $this->findRescuingPlayer();

                } else {
                    //距離が適正
                    if (($this->distance($this->rescuingPlayer) <= self::RescueRange) and $this->rescuingPlayer->isSneaking()) {
                        $this->rescueGauge++;
                        if ($this->rescueGauge === self::MaxRescueGauge) {
                            if (!$this->owner->isOnline()) return;
                            RescuePlayerPMMPService::execute($this->rescuingPlayer, $this->owner);
                        }

                        //距離が不適
                    } else {
                        $this->rescueGauge = 0;
                        $this->rescuingPlayer = null;

                    }
                }

                $this->sendCircleParticle();
            }
        ), 20 * 1, 20 * 1);

    }

    private function findRescuingPlayer() {
        $ownerData = TeamGameSystem::getPlayerData($this->owner);
        foreach ($this->getLevel()->getPlayers() as $player) {
            if ($player->isSneaking() and $player->distance($this) <= self::RescueRange) {
                $playerData = TeamGameSystem::getPlayerData($player);
                $playerEquipment = PlayerEquipmentsDAO::get($playerData->getName());
                if ($playerEquipment === null) continue;
                if ($playerEquipment->getMilitaryDepartment()->getName() !== MilitaryDepartment::NursingSoldier) continue;

                if ($ownerData->getTeamId() === null || $playerData->getTeamId() === null) continue;
                if ($ownerData->getTeamId()->equals($playerData->getTeamId())) {
                    $this->rescuingPlayer = $player;
                    break;
                }
            }
        }
    }

    private function sendCircleParticle() {
        for ($degree = 0; $degree <= 360; $degree += 20) {
            $center = $this->getPosition();

            $x = self::RescueRange * sin(deg2rad($degree));
            $z = self::RescueRange * cos(deg2rad($degree));

            $pos = $center->add($x, 1, $z);
            if ($this->rescuingPlayer === null) {
                $this->getLevel()->addParticle(new CriticalParticle($pos));

            } else if (!$this->rescuingPlayer->isOnline()) {
                $this->getLevel()->addParticle(new CriticalParticle($pos));

            } else {
                if ($degree <= ($this->rescueGauge / self::MaxRescueGauge * 360)) {
                    $this->getLevel()->addParticle(new DustParticle($pos, 0, 255, 0));
                } else {
                    $this->getLevel()->addParticle(new CriticalParticle($pos));
                }

            }
        }
    }

    protected function onDeath(): void {
        if ($this->limitTaskHandler !== null) $this->limitTaskHandler->cancel();
        if ($this->rescueTaskHandler !== null) $this->rescueTaskHandler->cancel();

        parent::onDeath();
    }

    /**
     * @return Player
     */
    public function getOwner(): Player {
        return $this->owner;
    }
}