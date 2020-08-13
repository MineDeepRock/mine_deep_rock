<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\Player;

class SpawnCadaverEntityPMMPService
{
    static function execute(Player $victim): void {
        $victimGameStatus = PlayerGameStatusStore::findByName($victim->getName());

        if ($victimGameStatus->isResuscitated()) {
            $nameTag = "[蘇生不可能]" . $victim->getName();
        } else {
            $nameTag = "[蘇生可能]" . $victim->getName();
        }

        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim);
        $cadaverEntity->setNameTag($nameTag);
        $cadaverEntity->spawnToAll();
    }
}