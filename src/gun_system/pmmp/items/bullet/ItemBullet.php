<?php


namespace gun_system\pmmp\items\bullet;


use gun_system\models\attachment\bullet\Bullet;
use pocketmine\item\Item;

abstract class ItemBullet extends Item
{
    private $bullet;

    public function __construct(int $id, string $name, Bullet $bullet) {
        $this->bullet = $bullet;
        parent::__construct($id, 0, $name);
    }

    /**
     * @return Bullet
     */
    public function getBullet(): Bullet {
        return $this->bullet;
    }
}