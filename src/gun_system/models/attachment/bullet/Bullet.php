<?php


namespace gun_system\models\attachment\bullet;


use gun_system\models\GunType;
use ValueObject;

abstract class Bullet extends ValueObject
{
    private $supportType;

    public function __construct(GunType $supportType) {
        $this->supportType = $supportType;
    }

    /**
     * @return GunType
     */
    public function getSupportType(): GunType {
        return $this->supportType;
    }
}