<?php


namespace game_system\pmmp\items;


use pocketmine\item\Item;

class AttachmentSelectItem extends Item
{
    public const ITEM_ID = Item::ARROW;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "AttachmentSelector");
    }
}