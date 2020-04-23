<?php


namespace gun_system\models\assault_rifle\attachiment\scope;


use gun_system\models\attachment\Magnification;

class TwelveFoldScopeForAR extends AssaultRifleScope
{
    public function __construct() {
        parent::__construct("12xScope", new Magnification(12));
    }
}