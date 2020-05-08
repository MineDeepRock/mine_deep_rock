<?php


namespace game_system\pmmp\Entity;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class GunDealerNPC extends NPCBase
{
    public $skinName = "GunDealer";
    public $geometryId = "geometry.GunDealer";
    public $geometryName = "GunDealer.geo.json";

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }
}