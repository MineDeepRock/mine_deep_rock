<?php


namespace gun_system\models\attachment;


use gun_system\models\GunType;

class Muzzle extends Attachment
{
    private $additionalPrecision;
    private $additionalDamage;

    public function __construct(string $name, GunType $supportGunType, float $additionalPrecision, float $additionalDamage) {
        parent::__construct($name, AttachmentType::Muzzle(), $supportGunType);
        $this->additionalPrecision = $additionalPrecision;
        $this->additionalDamage = $additionalDamage;
    }

    /**
     * @return float
     */
    public function getAddPrecision(): float {
        return $this->additionalPrecision;
    }

    /**
     * @return float
     */
    public function getAddDamage(): float {
        return $this->additionalDamage;
    }
}