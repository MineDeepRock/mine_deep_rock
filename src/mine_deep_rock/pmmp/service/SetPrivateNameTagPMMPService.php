<?php


namespace mine_deep_rock\pmmp\service;


use LogicException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;

class SetPrivateNameTagPMMPService
{
    static function execute(Player $player): void {
        $tag = PrivateNameTag::get($player);
        if ($tag !== null) {
            throw new LogicException("すでにプライベートネームタグがセットされています");
        }

        $health = $player->getHealth();

        if ($health <= 0) {
            $hpGauge = str_repeat(TextFormat::WHITE . "■", 20);
        } else if ($health >= 20) {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", 20);
        } else {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", $health);
            $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - $health);
        }

        $tag = new PrivateNameTag($player, "{$player->getName()} \n {$hpGauge}", [$player]);
        $tag->set();
    }
}