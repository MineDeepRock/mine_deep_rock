<?php


namespace gun_system\models;


use pocketmine\item\ItemIds;

class BulletId
{
    public const HAND_GUN = ItemIds::PAPER;
    public const ASSAULT_RIFLE = ItemIds::GLOWSTONE_DUST;
    public const LMG = ItemIds::FLINT;
    public const SNIPER_RIFLE = ItemIds::SUGAR;
    public const SMG = ItemIds::RABBIT_FOOT;

    public const BUCK_SHOT = ItemIds::BRICK;
    public const SLUG = ItemIds::NETHER_BRICK;
    public const DART = ItemIds::NETHER_WART;

    public static function fromGunType(GunType $gunType): int {
        switch ($gunType->getTypeText()) {
            case GunType::HandGun()->getTypeText():
                return self::HAND_GUN;
            case GunType::AssaultRifle()->getTypeText():
                return self::ASSAULT_RIFLE;
            case GunType::LMG()->getTypeText():
                return self::LMG;
            case GunType::Shotgun()->getTypeText()://TODO
                return self::BUCK_SHOT;
            case GunType::SniperRifle()->getTypeText():
                return self::SNIPER_RIFLE;
            case GunType::SMG()->getTypeText():
                return self::SMG;
        }
        return null;
    }
}


