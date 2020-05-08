<?php


namespace game_system\pmmp\Entity;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class GameMasterNPC extends NPCBase
{
    public $skinName = "GameMaster";
    public $geometryId = "geometry.GameMaster";
    public $geometryName = "GameMaster.geo.json";

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }
}