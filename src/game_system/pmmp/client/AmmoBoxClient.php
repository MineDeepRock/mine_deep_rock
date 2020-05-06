<?php


namespace game_system\pmmp\client;


use gun_system\models\GunType;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Level;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\math\Vector3;
use pocketmine\Server;

class AmmoBoxClient
{
    public function useAmmoBox(string $playerName, GunType $gunType, int $count): void {
        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(),
            "gun ammo \"" . $playerName . "\" " . $gunType->getTypeText() . " " . $count);
    }

    public function summonParticle(Level $level,Vector3 $pos){
        for($i = 0; $i < 6; ++$i){
            $level->addParticle(new HappyVillagerParticle(
                new Vector3(
                    $pos->getX() +  rand(-3,3),
                    $pos->getY() +  rand(0,3),
                    $pos->getZ() +  rand(-3,3))
            ));
        }
    }
}