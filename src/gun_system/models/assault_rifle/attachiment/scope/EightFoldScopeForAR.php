<?php


namespace gun_system\models\assault_rifle\attachiment\scope;


use gun_system\models\attachment\Magnification;

class EightFoldScopeForAR extends AssaultRifleScope
{
    public function __construct() {
        parent::__construct("8xScope", new Magnification(8));
    }
}