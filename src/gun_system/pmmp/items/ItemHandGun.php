<?php


namespace gun_system\pmmp\items;


use gun_system\models\hand_gun\HandGun;
use pocketmine\Player;

class ItemHandGun extends ItemGun
{
    public function __construct(string $name, HandGun $gun, Player $owner) { parent::__construct($name, $gun, $owner); }
}