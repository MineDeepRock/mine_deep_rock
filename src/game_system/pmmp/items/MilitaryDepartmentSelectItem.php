<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class MilitaryDepartmentSelectItem extends Item
{
    public const ITEM_ID = Item::COMPASS;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "兵科選択");
        $this->setCustomName($this->getName());
    }
}