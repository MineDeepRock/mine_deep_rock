<?php


namespace gun_system\models\attachment\scope;


class TwoFoldScope extends Scope
{
    public function __construct() {
        parent::__construct("2xScope", new Magnification(2));
    }
}