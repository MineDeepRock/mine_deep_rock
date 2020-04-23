<?php


namespace gun_system\models\assault_rifle\attachiment\magazine;


class ExpansionMagazineForSMG extends SubMachineGunMagazine
{
    public function __construct() {
        parent::__construct("ExpansionMagazine", 10, 1);
    }
}