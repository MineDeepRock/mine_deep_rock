<?php


namespace game_system\pmmp\Entity;


use pocketmine\level\Level;
use pocketmine\Player;

class GunDealerNPC extends NPCBase
{
    public $skinName = "GunDealer";
    public $geometryId = "geometry.GunDealer";
    public $geometryName = "GunDealer.geo.json";

    public function __construct(Level $level, Player $owner) {
        parent::__construct($level, $owner);
    }
}