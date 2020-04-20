<?php


namespace gun_system\pmmp\items;


use gun_system\models\sub_machine_gun\SubMachineGun;
use pocketmine\Player;

class ItemSubMachineGun extends ItemGun
{
    public function __construct(string $name, SubMachineGun $gun, Player $owner) { parent::__construct($name, $gun, $owner); }
}