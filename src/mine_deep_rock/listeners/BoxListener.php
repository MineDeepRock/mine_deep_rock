<?php


namespace mine_deep_rock\listeners;


use box_system\pmmp\events\AmmoBoxEffectOnEvent;
use box_system\pmmp\events\FlareBoxEffectOnEvent;
use box_system\pmmp\events\MedicineBoxEffectOnEvent;
use gun_system\GunSystem;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;

class BoxListener implements Listener
{
    public function onAmmoBoxEffect(AmmoBoxEffectOnEvent $event): void {
        $receiver = $event->getReceiver();
        GunSystem::giveAmmo($receiver, 0, 10);
        GunSystem::giveAmmo($receiver, 1, 10);
        //TODO:メッセージ
    }

    public function onMedicineBoxEffect(MedicineBoxEffectOnEvent $event): void {
        $receiver = $event->getReceiver();
        $receiver->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 2, 1));
        //TODO:メッセージ
    }

    public function onFlareBoxEffect(FlareBoxEffectOnEvent $event): void {
        //TODO:実装
    }
}