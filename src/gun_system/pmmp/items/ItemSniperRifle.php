<?php


namespace gun_system\pmmp\items;


use gun_system\models\sniper_rifle\SniperRifle;

class ItemSniperRifle extends ItemGun
{
    public function __construct(int $id, string $name, SniperRifle $gun) { parent::__construct($id, $name, $gun); }
}