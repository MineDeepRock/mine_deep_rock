<?php


namespace gun_system\models\sniper_rifle\attachment\scope;


use gun_system\models\attachment\Magnification;

class EightFoldScopeForSR extends SniperRifleScope
{
    public function __construct() {
        parent::__construct("8xScope", new Magnification(8));
    }
}