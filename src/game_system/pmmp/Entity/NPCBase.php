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

class NPCBase extends Human
{

    protected $skinId = "Standard_CustomSlim";
    protected $skinName = "";

    protected $capeData = "";

    protected $geometryId = "";
    protected $geometryName = "";

    public $width = 1;
    public $height = 2;
    public $eyeHeight = 1.5;

    protected $gravity = 0.08;
    protected $drag = 0.02;

    public $scale = 1.0;

    public $defaultHP = 1;
    public $uuid;

    public function __construct(Level $level, Player $owner) {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $owner->getX()),
                new DoubleTag('', $owner->getY() + 0.5),
                new DoubleTag('', $owner->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $owner->getYaw()),
                new FloatTag("", $owner->getPitch())
            ]),
        ]);
        $this->uuid = UUID::fromRandom();
        $this->initSkin();

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

    private function initSkin(): void {
        $this->setSkin(new Skin(
            $this->skinId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\\textures\\" . $this->skinName . ".skin"),
            $this->capeData,
            $this->geometryId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\models\\" . $this->geometryName)
        ));
    }

    public function getName(): string {
        return "";
    }
}