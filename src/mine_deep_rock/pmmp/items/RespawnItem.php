<?php

namespace mine_deep_rock\pmmp\items;


use pocketmine\item\Item;

class RespawnItem extends Item
{
    public const ITEM_ID = Item::EMERALD;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "リスポーン");
        $this->setCustomName($this->getName());
    }
}