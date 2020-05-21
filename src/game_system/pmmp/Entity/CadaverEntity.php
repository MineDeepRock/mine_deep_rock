<?php


namespace game_system\pmmp\Entity;


use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\utils\UUID;

class CadaverEntity extends Human
{

    //TODO:リファクタリング
    protected $skinId = "Standard_CustomSlim";
    protected $capeData = "";

    public $eyeHeight = 1.5;

    protected $gravity = 0.08;
    protected $drag = 0.02;

    public $scale = 1.0;

    public $defaultHP = 1;
    public $uuid;

    public $width = 0.6;
    public $height = 0.2;

    public $skinName = "Cadaver";
    public $geometryId = "geometry.Cadaver";
    public $geometryName = "Cadaver.geo.json";

    private $ownerName;

    public function __construct(Level $level, Player $owner) {
        $this->ownerName = $owner->getName();
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

    public function initEntity(): void {
        parent::initEntity();
        $this->setScale($this->scale);
        $this->setMaxHealth($this->defaultHP);
        $this->setHealth($this->getMaxHealth());
    }

    private function initSkin(Player $player): void {
        $this->setSkin(new Skin(
            $this->skinId,
            $player->getSkin()->getSkinData(),
            $this->capeData,
            $this->geometryId,
            file_get_contents("./plugin_data/MineDeepRock/models/" . $this->geometryName)
        ));
    }

    public function getName(): string {
        return "";
    }

    /**
     * @return string
     */
    public function getOwnerName(): string {
        return $this->ownerName;
    }
}