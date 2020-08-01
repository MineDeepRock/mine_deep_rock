<?php


namespace mine_deep_rock\pmmp\listener;


use box_system\pmmp\events\AmmoBoxEffectOnEvent;
use box_system\pmmp\events\FlareBoxEffectOnEvent;
use box_system\pmmp\events\MedicineBoxEffectOnEvent;
use gun_system\GunSystem;
use mine_deep_rock\pmmp\service\ShowPrivateNameTagToAllyPMMPService;
use mine_deep_rock\pmmp\service\ShowPrivateNameTagToParticipantsPMMPService;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_game_system\TeamGameSystem;

class BoxListener implements Listener
{
    /**
     * @var Server
     */
    private $server;
    /**
     * @var TaskScheduler
     */
    private $scheduler;

    public function __construct(Server $server, TaskScheduler $scheduler) {
        $this->server = $server;
        $this->scheduler = $scheduler;
    }

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
        $receiver = $event->getReceiver();
        $receiverData = TeamGameSystem::getPlayerData($receiver);
        if ($receiverData->getGameId() === null) return;
        ShowPrivateNameTagToParticipantsPMMPService::execute($receiver, $receiverData->getGameId());
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $i) use ($receiver, $receiverData) {
            ShowPrivateNameTagToAllyPMMPService::execute($receiver, $receiverData->getTeamId());
        }), 20 * 3);
        //TODO:メッセージ
    }
}