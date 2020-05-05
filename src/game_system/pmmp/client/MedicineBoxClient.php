<?php


namespace game_system\pmmp\client;


use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;

class MedicineBoxClient
{
    public function useMedicineBox(Player $player): void {
        $player->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 3, 2, false));
    }
}