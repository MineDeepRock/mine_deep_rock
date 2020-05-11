<?php


namespace game_system\pmmp\client;


use pocketmine\level\Level;
use pocketmine\level\particle\AngryVillagerParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FlareBoxClient
{
    public function summonParticle(Level $level, Vector3 $pos) {
        for ($i = 0; $i < 6; ++$i) {
            $level->addParticle(new AngryVillagerParticle(
                new Vector3(
                    $pos->getX() + rand(-3, 3),
                    $pos->getY() + rand(0, 3),
                    $pos->getZ() + rand(-3, 3))
            ));
        }
    }

    public function effectOn(Player $owner, Player $player): void {
        $player->sendPopup("スポットされました。4秒間自分のネームタグが表示されます");
        $owner->sendPopup("敵をスポットしました。4秒間敵のネームタグを表示します");

        $player->setNameTagAlwaysVisible(true);
    }

    public function release(Player $player): void {
        $player->setNameTagAlwaysVisible(false);
    }
}