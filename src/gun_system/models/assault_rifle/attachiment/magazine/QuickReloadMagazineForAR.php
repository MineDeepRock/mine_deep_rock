<?php


namespace gun_system\models\assault_rifle\attachiment\magazine;


class QuickReloadMagazineForAR extends AssaultRifleMagazine
{
    public function __construct() {
        parent::__construct("QuickReloadMagazine", 0, -1);
    }
}