<?php


namespace gun_system\models\assault_rifle\attachiment\magazine;


class ExpansionMagazineForHG extends HandGunMagazine
{
    public function __construct() {
        parent::__construct("ExpansionMagazine", 8, 1);
    }
}