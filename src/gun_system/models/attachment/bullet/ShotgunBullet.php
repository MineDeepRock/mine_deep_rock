<?php


namespace gun_system\models\attachment\bullet;


use gun_system\models\GunType;

abstract class ShotgunBullet extends Bullet
{
    private $bulletType;

    public function __construct(ShotgunBulletType $bulletType) {
        parent::__construct("ShotgunBullet", GunType::Shotgun());
        $this->bulletType = $bulletType;
    }

    /**
     * @return ShotgunBulletType
     */
    public function getBulletType(): ShotgunBulletType {
        return $this->bulletType;
    }
}