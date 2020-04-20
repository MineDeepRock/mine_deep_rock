<?php


namespace gun_system\pmmp\items;


use gun_system\models\sub_machine_gun\SubMachineGun;

class ItemSubMachineGun extends ItemGun
{
    public function __construct(string $name, SubMachineGun $gun) { parent::__construct($name, $gun); }
}