<?php


namespace gun_system\pmmp\items;


use gun_system\models\hand_gun\HandGun;

class ItemHandGun extends ItemGun
{
    public function __construct(string $name, HandGun $gun) { parent::__construct($name, $gun); }
}