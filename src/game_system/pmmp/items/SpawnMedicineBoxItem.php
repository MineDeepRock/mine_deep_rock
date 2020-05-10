<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class SpawnMedicineBoxItem extends Item
{
    public const ITEM_ID = Item::GOLD_INGOT;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "医療箱");
        $this->setCustomName($this->getName());
    }
}