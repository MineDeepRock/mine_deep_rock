<?php


namespace mine_deep_rock\listeners;


use box_system\pmmp\events\AmmoBoxEffectOnEvent;
use box_system\pmmp\events\BoxStopEvent;
use box_system\pmmp\events\FlareBoxEffectOnEvent;
use box_system\pmmp\events\MedicineBoxEffectOnEvent;
use gun_system\GunSystem;
use military_department_system\models\GadgetType;
use mine_deep_rock\controllers\NameTagController;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_system\TeamSystem;

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
        $receiverData = TeamSystem::getPlayerData($receiver->getName());
        if ($receiverData->getJoinedGameId() === null) return;
        NameTagController::showToParticipant($receiver, $receiverData->getJoinedGameId(), $receiverData->getBelongTeamId(), $this->server);
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $i) use ($receiver, $receiverData) {
            NameTagController::showToAlly($receiver, $receiverData->getJoinedGameId(), $receiverData->getBelongTeamId(), $this->server);
        }), 20 * 3);
        //TODO:メッセージ
    }

    public function onStopBox(BoxStopEvent $event): void {
        $player = $event->getOwner();
        if (!$player->isOnline()) return;
        if ($player->getLevel()->getName() === "lobby") return;
        if ($player->getGamemode() !== Player::ADVENTURE) return;

        $type = new GadgetType($event->getBox()::NAME);
        if (!$player->getInventory()->contains($type->toItem())) {
            $player->getInventory()->addItem($type->toItem());
        }
    }
}