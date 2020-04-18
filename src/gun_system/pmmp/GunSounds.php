<?php


namespace gun_system\pmmp;


use gun_system\models\GunType;

class GunSounds
{
    private $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public static function HandGunShoot(): GunSounds {
        return new GunSounds("gun.handgun.shoot");
    }

    public static function AssaultRifleShoot(): GunSounds {
        return new GunSounds("gun.assaultrifle.shoot");
    }

    public static function LMGShoot(): GunSounds {
        return new GunSounds("gun.lmg.shoot");
    }

    public static function ShotgunShoot(): GunSounds {
        return new GunSounds("gun.shotgun.shoot");
    }

    public static function SniperRifleShoot(): GunSounds {
        return new GunSounds("gun.sniperrifle.shoot");
    }

    public static function SMGShoot(): GunSounds {
        return new GunSounds("gun.smg.shoot");
    }


    public static function shootSoundFromGunType(GunType $gunType) {
        switch ($gunType->getTypeText()) {
            case "HandGun":
                return self::HandGunShoot();
            case "AssaultRifle":
                return self::AssaultRifleShoot();
            case "LMG":
                return self::LMGShoot();
            case "Shotgun":
                return self::ShotgunShoot();
            case "SniperRifle":
                return self::SniperRifleShoot();
            case "SMG":
                return self::SMGShoot();
        }
    }

    /**
     * @return mixed
     */
    public function getTypeText() {
        return $this->type;
    }
}