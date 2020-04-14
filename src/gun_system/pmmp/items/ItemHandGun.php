<?php


namespace gun_system\pmmp\items;


use gun_system\models\hand_gun\HandGun;

abstract class ItemHandGun extends ItemGun
{
    public function __construct(int $id, string $name, HandGun $gun) { parent::__construct($id, $name, $gun); }
}