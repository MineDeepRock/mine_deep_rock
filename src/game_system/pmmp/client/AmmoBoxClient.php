<?php


namespace game_system\pmmp\client;


use game_system\model\AmmoBox;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\Server;

class AmmoBoxClient
{
    private $ammoBox;

    public function __construct() {
        $this->ammoBox = new AmmoBox(40);
    }

    public function useAmmoBox(Player $player): void {
        $item = $player->getInventory()->getItemInHand();
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            $gun = $item->getGunData();
            if ($this->ammoBox->isAlreadyUsed($player->getName(), $gun::NAME)) return;

            Server::getInstance()->dispatchCommand(
                new ConsoleCommandSender(),
                "gun ammo " . $player->getName() . " " . $gun->getType()->getTypeText());

            $this->ammoBox->addPlayerUsed($player->getName(), $gun::NAME);
        }
    }

    /**
     * @return AmmoBox
     */
    public function getAmmoBox(): AmmoBox {
        return $this->ammoBox;
    }
}