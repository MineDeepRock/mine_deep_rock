<?php


namespace gun_system\pmmp\items;


use gun_system\models\Gun;

class ItemSubMachineGun extends ItemGun
{
    public function __construct(string $name, Gun $gun) { parent::__construct($name, $gun); }
}