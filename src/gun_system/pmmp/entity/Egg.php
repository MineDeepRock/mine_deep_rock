<?php


namespace gun_system\pmmp\entity;


use gun_system\pmmp\GunSounds;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
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
            $distance = $blockPos->distance($player->getPosition());
            if ($distance <= 5) {
                GunSounds::play($player, GunSounds::bulletHitBlock());
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 20 * 3, 1));
                $item = $player->getInventory()->getItemInHand();
                if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun"))
                    $item->scare();
            } else if ($distance <= 10) {
                GunSounds::play($player, GunSounds::bulletFly());
            }
        }

        parent::onHitBlock($blockHit, $hitResult);
    }

    protected function onHit(ProjectileHitEvent $event): void {

    }
}