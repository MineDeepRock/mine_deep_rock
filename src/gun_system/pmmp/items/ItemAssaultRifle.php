<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\AssaultRifle;
use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ItemAssaultRifle extends ItemGun
{

    public function __construct(string $name, AssaultRifle $gun) { parent::__construct($name, $gun); }
}