<?php


namespace gun_system\models\shotgun\attachment\muzzle;


class FullChoke extends ShotgunMuzzle
{
    public function __construct() {
        parent::__construct("FullChoke", 5, -2);
    }
}