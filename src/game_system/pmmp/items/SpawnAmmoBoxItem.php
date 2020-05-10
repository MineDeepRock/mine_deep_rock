<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class SpawnAmmoBoxItem extends Item
{
    public const ITEM_ID = Item::IRON_INGOT;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "弾薬箱");
        $this->setCustomName($this->getName());
    }
}