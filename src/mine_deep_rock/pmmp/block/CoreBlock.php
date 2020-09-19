<?php

namespace mine_deep_rock\pmmp\block;


use pocketmine\block\Block;
use pocketmine\block\EndStone;

class CoreBlock extends EndStone
{
    static function getBlockId():int {
        return Block::END_STONE;
    }

}