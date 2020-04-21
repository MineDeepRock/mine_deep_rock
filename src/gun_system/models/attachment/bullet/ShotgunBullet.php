<?php


namespace gun_system\models\attachment\bullet;


use gun_system\models\GunType;

class ShotgunBullet extends Bullet
{
    public function __construct() {
        parent::__construct("ShotgunBullet",GunType::Shotgun());
    }
}

class ShotgunBulletType {
    private $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public function equal(ShotgunBulletType $gunType) :bool {
        return $this->type == $gunType->type;
    }

    public static function Buckshot():ShotgunBulletType {
        return new ShotgunBulletType("Buckshot");
    }

    public static function Slug():ShotgunBulletType {
        return new ShotgunBulletType("Slug");
    }

    public static function Dart():ShotgunBulletType {
        return new ShotgunBulletType("Dart");
    }

    public static function Frag():ShotgunBulletType {
        return new ShotgunBulletType("Frag");
    }
}