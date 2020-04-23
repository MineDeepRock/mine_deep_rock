<?php


namespace gun_system\models\assault_rifle\attachiment\magazine;


class QuickReloadMagazineForHG extends HandGunMagazine
{
    public function __construct() {
        parent::__construct("QuickReloadMagazine", 0, -1);
    }
}