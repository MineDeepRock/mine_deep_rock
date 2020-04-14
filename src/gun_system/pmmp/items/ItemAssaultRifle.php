<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\AssaultRifle;

abstract class ItemAssaultRifle extends ItemGun
{
    public function __construct(int $id, string $name, AssaultRifle $gun) { parent::__construct($id, $name, $gun); }

}