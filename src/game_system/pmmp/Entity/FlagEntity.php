<?php


namespace game_system\pmmp\Entity;


use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class FlagEntity extends NPCBase
{
    public $skinName = "WhiteFlag";
    public $geometryId = "geometry.Flag";
    public $geometryName = "Flag.geo.json";

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }

    public function changeColorWhite(): void {
        $this->setSkin(new Skin(
            $this->skinId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\\textures\WhiteFlag.skin"),
            $this->capeData,
            $this->geometryId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\models\\" . $this->geometryName)
        ));
        $this->sendSkin();
    }

    public function changeColorRed(): void {
        $this->setSkin(new Skin(
            $this->skinId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\\textures\RedFlag.skin"),
            $this->capeData,
            $this->geometryId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\models\\" . $this->geometryName)
        ));
        $this->sendSkin();
    }

    public function changeColorBlue(): void {
        $this->setSkin(new Skin(
            $this->skinId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\\textures\BlueFlag.skin"),
            $this->capeData,
            $this->geometryId,
            file_get_contents("D:\pmmp\plugins\mine_deep_rock\src\game_system\pmmp\Entity\models\\" . $this->geometryName)
        ));
        $this->sendSkin();
    }
}