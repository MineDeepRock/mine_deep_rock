<?php


namespace gun_system\pmmp\items;


use gun_system\models\revolver\Revolver;
use pocketmine\Player;

class ItemRevolver extends ItemGun
{
public function __construct(string $name, Revolver $gun, Player $owner) { parent::__construct($name, $gun, $owner); }
}