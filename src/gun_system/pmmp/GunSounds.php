<?php


namespace gun_system\pmmp;


use gun_system\models\GunType;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;

class GunSounds extends ShootSounds
{
    private $text;

    public function __construct($text) {
        $this->text = $text;
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

    public static function bulletFly(): GunSounds {
        return new GunSounds("gun.bullet.fly");
    }

    public static function bulletHitBlock(): GunSounds {
        return new GunSounds("gun.bullet.hit.block");
    }

    public static function bulletHitPlayer():GunSounds {
        return new GunSounds("game.player.hurt");
    }

    public static function play(Player $owner, GunSounds $soundName,int $volume = 10,int $pitch = 2): void {
        $packet = new PlaySoundPacket();
        $packet->x = $owner->x;
        $packet->y = $owner->y;
        $packet->z = $owner->z;
        $packet->volume = $volume;
        $packet->pitch = $pitch;
        $packet->soundName = $soundName->getText();
        $owner->sendDataPacket($packet);
    }

    public static function playAround(Player $owner, GunSounds $soundName): void {
        $players = $owner->getServer()->getOnlinePlayers();
        self::play($owner, $soundName);

        foreach ($players as $player) {
            $distance = $owner->getPosition()->distance($player->getPosition());
            if ($distance < 20) {
                $packet = new PlaySoundPacket();
                $packet->x = $player->x;
                $packet->y = $player->y;
                $packet->z = $player->z;
                $packet->volume = 5 - $distance / 4;
                $packet->pitch = 2;
                $packet->soundName = $soundName->getText();
                $player->sendDataPacket($packet);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getText() {
        return $this->text;
    }
}

class ShootSounds extends ReloadSounds
{
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
            case "Revolver":
                return self::RevolverShoot();
        }
        return new GunSounds("");
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

    public static function RevolverShoot(): GunSounds {
        return new GunSounds("gun.assaultrifle.shoot");
    }
}

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