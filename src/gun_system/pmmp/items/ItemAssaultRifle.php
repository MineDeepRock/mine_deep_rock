<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\AssaultRifle;
use pocketmine\Player;

class ItemAssaultRifle extends ItemGun
{

    public function __construct(string $name, AssaultRifle $gun, Player $owner) { parent::__construct($name, $gun, $owner); }
}