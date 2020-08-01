<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\entity\CadaverEntity;
use pocketmine\Player;

class SpawnCadaverEntityPMMPService
{
    static function execute(Player $victim): void {
        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim);
        $cadaverEntity->spawnToAll();
    }
}