<?php


namespace gun_system\models\assault_rifle\attachiment\magazine;


class QuickReloadMagazine extends AssaultRifleMagazine
{
    public function __construct() {
        parent::__construct("QuickReloadMagazine", 0, -1);
    }
}