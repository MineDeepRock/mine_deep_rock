<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\AssaultRifle;

class ItemAssaultRifle extends ItemGun
{
    public function __construct(string $name, AssaultRifle $gun) { parent::__construct($name, $gun); }

}