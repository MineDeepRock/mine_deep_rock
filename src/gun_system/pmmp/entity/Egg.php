<?php


namespace gun_system\pmmp\entity;


use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\particle\ItemBreakParticle;

class Egg extends Throwable{
    public const NETWORK_ID = self::EGG;

    protected $gravity = 0;

    protected function onHit(ProjectileHitEvent $event) : void{
        for($i = 0; $i < 6; ++$i){
            $this->level->addParticle(new ItemBreakParticle($this, ItemFactory::get(Item::EGG)));
        }
    }
}