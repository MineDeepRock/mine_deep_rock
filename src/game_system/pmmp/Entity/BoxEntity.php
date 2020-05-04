<?php


namespace game_system\pmmp\Entity;


use game_system\pmmp\client\AmmoBoxClient;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\UUID;

class BoxEntity extends Human
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

    public function __construct(Level $level, CompoundTag $nbt) {
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
        $path = "D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\\textures\\" . $this->skinName . ".png";
        $img = imagecreatefrompng($path);
        $skinbytes = '';
        $l = (int)getimagesize($path)[1];

        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $argb = imagecolorat($img, $x, $y);
                $a = ((~((int)($argb >> 24))) << 1) & 0xff;
                $r = ($argb >> 16) & 0xff;
                $g = ($argb >> 8) & 0xff;
                $b = $argb & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        imagedestroy($img);

        $this->setSkin(new Skin(
            $this->skinId,
            $skinbytes,
            $this->capeData,
            $this->geometryId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\models\\" . $this->geometryName)
        ));
    }

    public function getName(): string {
        return "";
    }
}