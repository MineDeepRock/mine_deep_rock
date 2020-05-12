<?php


namespace gun_system\pmmp;


class ReloadSounds
{
    public static function MagazineOut(): GunSounds {
        return new GunSounds("gun.reload.magazine.out");
    }
    public static function MagazineIn(): GunSounds {
        return new GunSounds("gun.reload.magazine.in");
    }

    public static function ClipPush(): GunSounds {
        return new GunSounds("gun.reload.clip.push");
    }

    public static function ClipPing(): GunSounds {
        return new GunSounds("gun.reload.clip.ping");
    }

    public static function ReloadOne(): GunSounds {
        return new GunSounds("gun.reload.clip.one");
    }
}