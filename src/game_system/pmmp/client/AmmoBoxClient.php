<?php


namespace game_system\pmmp\client;


use gun_system\models\GunType;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Level;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

class AmmoBoxClient
{
    public function useAmmoBox(Player $owner,Player $player, GunType $gunType, int $count): void {
        $player->sendPopup($owner->getName() . "から弾薬補給を受けました");
        $owner->sendPopup($player->getName() . "に弾薬をわたしました+2");

        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(),
            "gun ammo \"" . $player->getName() . "\" " . $gunType->getTypeText() . " " . $count);
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