<?php


namespace gun_system\models\attachment;


use gun_system\models\GunType;

class Magazine extends Attachment
{
    private $additionalBullets;
    private $additionalReloadTime;

    public function __construct(string $name, GunType $supportGunType, int $additionalBullets, float $additionalReloadTime) {
        $this->additionalBullets = $additionalBullets;
        $this->additionalReloadTime = $additionalReloadTime;
        parent::__construct($name, AttachmentType::Magazine(), $supportGunType);
    }

    /**
     * @return int
     */
    public function getAdditionalBullets(): int {
        return $this->additionalBullets;
    }

    /**
     * @return float
     */
    public function getAdditionalReloadTime(): float {
        return $this->additionalReloadTime;
    }
}