<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;

class SetPrivateNameTagPMMPService
{
    static function execute(Player $player): void {
        $tag = PrivateNameTag::get($player);
        if ($tag !== null) {
            //TODO : エラーを吐くように
            return;
        }

        $hpGauge = str_repeat(TextFormat::GREEN . "■", intval($player->getHealth()));
        $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - intval($player->getHealth()));
        $tag = new PrivateNameTag($player, "{$player->getName()} \n {$hpGauge}", []);
        $tag->set();
    }
}