<?php


namespace gun_system\models\attachment;


use gun_system\models\GunType;

class Barrel extends Attachment
{
    private $bulletDamagePer;
    private $bulletSpeedPer;
    private $precisionPer;
    private $reactionPer;

    public function __construct(string $name, GunType $supportGunType, $bulletDamagePer, $bulletSpeedPer, $precisionPer, $reactionPer) {
        parent::__construct($name, AttachmentType::Barrel(), $supportGunType);
        $this->bulletDamagePer = $bulletDamagePer;
        $this->bulletSpeedPer = $bulletSpeedPer;
        $this->precisionPer = $precisionPer;
        $this->reactionPer = $reactionPer;
    }
}