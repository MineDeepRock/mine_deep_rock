<?php


namespace gun_system\models\sniper_rifle\attachment\scope;


use gun_system\models\attachment\Magnification;

class TwelveFoldScopeForSR extends SniperRifleScope
{
    public function __construct() {
        parent::__construct("12xScope", new Magnification(12));
    }
}