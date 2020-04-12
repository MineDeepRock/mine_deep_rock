<?php


namespace gun_system\models;


class HandGun extends Gun
{
    public function __construct() {
        parent::__construct(5, new GunRate(1), 10, 1, new ReloadDuration(3), 10);
    }
}