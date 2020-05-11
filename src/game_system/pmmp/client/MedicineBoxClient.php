<?php


namespace game_system\pmmp\client;


use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\level\Level;
use pocketmine\level\particle\HeartParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;

class MedicineBoxClient
{
    public function useMedicineBox(Player $owner,Player $player): void {
        $player->sendPopup($owner->getName() . "から回復を受けました");
        $owner->sendPopup($player->getName() . "を治療をしました+2");

        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 3, 2, false));
    }

    public function summonParticle(Level $level,Vector3 $pos){
        for($i = 0; $i < 6; ++$i){
            $level->addParticle(new HeartParticle(
                new Vector3(
                    $pos->getX() +  rand(-3,3),
                    $pos->getY() +  rand(0,3),
                    $pos->getZ() +  rand(-3,3))
            ));
        }
    }
}