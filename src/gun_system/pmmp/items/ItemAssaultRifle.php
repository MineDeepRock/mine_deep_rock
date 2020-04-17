<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\AssaultRifle;
use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ItemAssaultRifle extends ItemGun
{

    public function __construct(string $name, AssaultRifle $gun) { parent::__construct($name, $gun); }

    public function playShootingSound(Player $player): void {
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $pos = new Vector3($x, $y, $z);
        $player->getLevel()->addSound(new AnvilBreakSound($pos));
    }
}