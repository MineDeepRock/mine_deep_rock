<?php


namespace game_system\pmmp\Entity;


use pocketmine\level\Level;
use pocketmine\Player;

class GameMasterNPC extends NPCBase
{
    public $skinName = "GameMaster";
    public $geometryId = "geometry.GameMaster";
    public $geometryName = "GameMaster.geo.json";

    public function __construct(Level $level, Player $owner) {
        parent::__construct($level, $owner);
    }
}