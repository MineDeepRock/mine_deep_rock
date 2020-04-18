<?php


namespace gun_system\pmmp\items;


use gun_system\models\hand_gun\HandGun;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ItemHandGun extends ItemGun
{
    public function __construct(string $name, HandGun $gun) { parent::__construct($name, $gun); }
}