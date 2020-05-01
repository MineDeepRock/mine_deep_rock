<?php


namespace game_system\pmmp\Entity;


use game_system\GameSystemListener;
use gun_system\pmmp\GunSounds;
use gun_system\pmmp\items\ItemShotGun;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\Server;

class Egg extends Throwable
{
    public const NETWORK_ID = self::EGG;

    protected $gravity = 0;

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void {
        $blockPos = new Vector3(
            $blockHit->getX(),
            $blockHit->getY(),
            $blockHit->getZ()
        );

        $blockHit->getLevel()->addParticle(new ExplodeParticle($blockPos));

        $players = Server::getInstance()->getOnlinePlayers();

        foreach ($players as $player) {
            if ($player !== null || $this->getOwningEntity() !== null) {

                $distance = $blockPos->distance($player->getPosition());
                if ($distance <= 5) {
                    GunSounds::play($player, GunSounds::bulletHitBlock());
                    GameSystemListener::getInstance()->scare($player,$this->getOwningEntity());
                } else if ($distance <= 10) {
                    GunSounds::play($player, GunSounds::bulletFly());
                }
            }
        }

        parent::onHitBlock($blockHit, $hitResult);
    }

    protected function onHit(ProjectileHitEvent $event): void {

    }
}