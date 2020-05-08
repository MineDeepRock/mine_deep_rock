<?php


namespace game_system\pmmp\Entity;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class TrialGunDealerNPC extends NPCBase
{
    public $skinName = "TrialGunDealer";
    public $geometryId = "geometry.TrialGunDealer";
    public $geometryName = "TrialGunDealer.geo.json";

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }
}