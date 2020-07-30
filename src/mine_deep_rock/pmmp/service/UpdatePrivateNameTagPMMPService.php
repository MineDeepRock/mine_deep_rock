<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;

class UpdatePrivateNameTagPMMPService
{
    static function execute(Player $target): void {
        $tag = PrivateNameTag::get($target);
        if ($tag === null) {
            //TODO : エラーを吐くように
            return;
        }

        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($target->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($target->getHealth()));
        $tag->updateNameTag("{$target->getName()} \n {$hpGauge}");
    }
}