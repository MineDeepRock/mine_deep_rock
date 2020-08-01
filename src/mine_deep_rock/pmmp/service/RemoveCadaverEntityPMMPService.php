<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\entity\CadaverEntity;
use pocketmine\Player;

class RemoveCadaverEntityPMMPService
{
    static function execute(Player $player): void {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $player->getName()) {
                    $entity->kill();
                }
            }
        }
    }
}