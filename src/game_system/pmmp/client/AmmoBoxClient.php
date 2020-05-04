<?php


namespace game_system\pmmp\client;


use gun_system\models\GunType;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;

class AmmoBoxClient
{
    public function useAmmoBox(string $playerName, GunType $gunType, int $count): void {
        Server::getInstance()->dispatchCommand(
            new ConsoleCommandSender(),
            "gun ammo " . $playerName . " " . $gunType->getTypeText() . " " . $count);
    }
}