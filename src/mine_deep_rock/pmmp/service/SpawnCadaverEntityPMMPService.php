<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;

class SpawnCadaverEntityPMMPService
{
    static function execute(Player $victim, TaskScheduler $scheduler): void {
        $victimGameStatus = PlayerGameStatusStore::findByName($victim->getName());

        if ($victimGameStatus->isResuscitated()) {
            $nameTag = TextFormat::GREEN . "[蘇生不可能]" . $victim->getName();
        } else {
            $nameTag = TextFormat::RED . "[蘇生可能]" . $victim->getName();
        }

        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim, $scheduler);
        $cadaverEntity->setNameTag($nameTag);
        $cadaverEntity->spawnToAll();
    }
}