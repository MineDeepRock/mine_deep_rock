<?php


namespace gun_system\pmmp\entity;


use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class Arrow extends Projectile
{
    public const NETWORK_ID = self::ARROW;

    public $width = 0.25;
    public $height = 0.25;

    protected $gravity = 0.0;
    protected $drag = 0.01;

    /** @var int */
    protected $collideTicks = 0;

    public function __construct(Level $level, CompoundTag $nbt, ?Entity $shootingEntity = null){
        parent::__construct($level, $nbt, $shootingEntity);
    }

    public function entityBaseTick(int $tickDiff = 1) : bool{
        if($this->closed){
            return false;
        }

        $hasUpdate = parent::entityBaseTick($tickDiff);

        if($this->blockHit !== null){
            $this->collideTicks += $tickDiff;
            if($this->collideTicks > 40){
                $this->flagForDespawn();
                $hasUpdate = true;
            }
        }else{
            $this->collideTicks = 0;
        }

        return $hasUpdate;
    }

    protected function onHit(ProjectileHitEvent $event) : void{
        if (!$this->isClosed())
            $this->close();
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult) : void{
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
    }

    public function onCollideWithPlayer(Player $player) : void{
    }
}