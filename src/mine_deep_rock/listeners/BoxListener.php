<?php


namespace mine_deep_rock\listeners;


use box_system\pmmp\events\AmmoBoxEffectOnEvent;
use box_system\pmmp\events\FlareBoxEffectOnEvent;
use box_system\pmmp\events\MedicineBoxEffectOnEvent;
use gun_system\GunSystem;
use pocketmine\event\Listener;

class BoxListener implements Listener
{
    public function onAmmoBoxEffect(AmmoBoxEffectOnEvent $event): void {
        GunSystem::giveAmmo($event->getReceiver(), 0, 10);
        GunSystem::giveAmmo($event->getReceiver(), 1, 10);
        //TODO:メッセージ
    }
    public function onMedicineBoxEffect(MedicineBoxEffectOnEvent $event): void {
        //TODO:実装
    }
    public function onFlareBoxEffect(FlareBoxEffectOnEvent $event): void {
        //TODO:実装
    }
}