<?php


namespace mine_deep_rock\pmmp\entities;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class TeamDeathMatchNPC extends NPCBase
{
    const NAME = "TeamDeathMatchNPC";
    public $width = 0.6;
    public $height = 1.8;

    public $skinName = self::NAME;
    public $geometryId = "geometry." . self::NAME;
    public $geometryName = self::NAME . ".geo.json";

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }
}