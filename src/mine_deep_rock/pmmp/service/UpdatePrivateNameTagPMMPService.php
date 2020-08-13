<?php


namespace mine_deep_rock\pmmp\service;


use LogicException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use private_name_tag\models\PrivateNameTag;

class UpdatePrivateNameTagPMMPService
{
    static function execute(Player $target, ?int $health = null): void {
        $tag = PrivateNameTag::get($target);
        if ($tag === null) {
            SetPrivateNameTagPMMPService::execute($target);
            //TODO: throw new LogicException("プライベートネームタグがセットされていません");
        }

        $health = $health ?? $target->getHealth();

        if ($health <= 0) {
            $hpGauge = str_repeat(TextFormat::WHITE . "■", 20);
        } else if ($health >= 20) {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", 20);
        } else {
            $hpGauge = str_repeat(TextFormat::GREEN . "■", $health);
            $hpGauge .= str_repeat(TextFormat::WHITE . "■", 20 - $health);
        }

        $tag->updateNameTag("{$target->getName()} \n {$hpGauge}");
    }
}