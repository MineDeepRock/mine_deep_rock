<?php


namespace gun_system\pmmp;


use gun_system\models\GunType;

class GunSounds
{
    private $text;

    public function __construct($text) {
        $this->text = $text;
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

    public static function HandGunStartReloading(): GunSounds {
        return new GunSounds("gun.handgun.reload.start");
    }

    public static function AssaultRifleStartReloading(): GunSounds {
        return new GunSounds("gun.assaultrifle.reload.start");
    }

    public static function LMGStartReloading(): GunSounds {
        return new GunSounds("gun.lmg.reload.start");
    }

    public static function SniperRifleStartReloading(): GunSounds {
        return new GunSounds("gun.sniperrifle.reload.start");
    }

    public static function SMGStartReloading(): GunSounds {
        return new GunSounds("gun.smg.reload.start");
    }

    public static function HandGunEndReloading(): GunSounds {
        return new GunSounds("gun.handgun.reload.end");
    }

    public static function AssaultRifleEndReloading(): GunSounds {
        return new GunSounds("gun.assaultrifle.reload.end");
    }

    public static function LMGEndReloading(): GunSounds {
        return new GunSounds("gun.lmg.reload.end");
    }

    public static function SniperRifleEndReloading(): GunSounds {
        return new GunSounds("gun.sniperrifle.reload.end");
    }

    public static function SMGEndReloading(): GunSounds {
        return new GunSounds("gun.smg.reload.end");
    }

    public static function ShotgunPumpAction(): GunSounds {
        return new GunSounds("gun.shotgun.pumpaction");
    }

    public static function SniperRifleCocking(): GunSounds {
        return new GunSounds("gun.sniperrifle.cocking");
    }

    //TODO:実装
    public static function outOfBullet(): GunSounds {
        return new GunSounds("gun.outofbullet");
    }

    public static function LMGReady(): GunSounds {
        return new GunSounds("gun.lmg.ready");
    }

    public static function LMGOverheat(): GunSounds {
        return new GunSounds("gun.lmg.overheat");
    }

    public static function ShotgunReload(): GunSounds {
        return new GunSounds("gun.shotgun.reload");
    }

    public static function shootSoundFromGunType(GunType $gunType): GunSounds {
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
        return new GunSounds("");
    }

    public static function startReloadingSoundFromGunType(GunType $gunType): GunSounds {
        switch ($gunType->getTypeText()) {
            case "HandGun":
                return self::HandGunStartReloading();
            case "AssaultRifle":
                return self::AssaultRifleStartReloading();
            case "LMG":
                return self::LMGStartReloading();
            case "SniperRifle":
                return self::SniperRifleStartReloading();
            case "SMG":
                return self::SMGStartReloading();
        }
        return new GunSounds("");
    }

    public static function endReloadingSoundFromGunType(GunType $gunType): GunSounds {
        switch ($gunType->getTypeText()) {
            case "HandGun":
                return self::HandGunEndReloading();
            case "AssaultRifle":
                return self::AssaultRifleEndReloading();
            case "LMG":
                return self::LMGEndReloading();
            case "SniperRifle":
                return self::SniperRifleEndReloading();
            case "SMG":
                return self::SMGEndReloading();
        }
        return new GunSounds("");
    }

    /**
     * @return mixed
     */
    public function getText() {
        return $this->text;
    }
}