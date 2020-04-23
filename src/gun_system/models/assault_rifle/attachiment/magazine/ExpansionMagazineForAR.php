<?php


namespace gun_system\models\assault_rifle\attachiment\magazine;


class ExpansionMagazineForAR extends AssaultRifleMagazine
{
    public function __construct() {
        parent::__construct("ExpansionMagazine", 7, 1);
    }
}