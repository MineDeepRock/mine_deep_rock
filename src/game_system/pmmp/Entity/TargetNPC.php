<?php


namespace game_system\pmmp\Entity;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class TargetNPC extends NPCBase
{
    public $skinName = "Target";
    public $geometryId = "geometry.Target";
    public $geometryName = "Target.geo.json";

    public $defaultHP = 20;

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }

    protected function onDeath(): void {
        parent::onDeath();
    }
}