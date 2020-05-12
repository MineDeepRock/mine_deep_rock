<?php


namespace gun_system\pmmp;


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
            if ($distance < 30) {
                $packet = new PlaySoundPacket();
                $packet->x = $player->x;
                $packet->y = $player->y;
                $packet->z = $player->z;
                $packet->volume = 3 - $distance/10;
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
