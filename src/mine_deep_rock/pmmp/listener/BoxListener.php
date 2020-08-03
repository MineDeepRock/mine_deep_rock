<?php


namespace mine_deep_rock\pmmp\listener;


use box_system\pmmp\events\AmmoBoxEffectOnEvent;
use box_system\pmmp\events\BoxStopEvent;
use box_system\pmmp\events\FlareBoxEffectOnEvent;
use box_system\pmmp\events\MedicineBoxEffectOnEvent;
use box_system\pmmp\items\BoxItem;
use gun_system\GunSystem;
use mine_deep_rock\dao\PlayerStatusDAO;
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
        $owner = $event->getOwner();
        $receiver = $event->getReceiver();
        GunSystem::giveAmmo($receiver, 0, 10);
        GunSystem::giveAmmo($receiver, 1, 10);

        $receiver->sendTip("{$owner->getName()}から弾薬を供給");
        $owner->sendTip("{$receiver->getName()}に弾薬を供給");
        //TODO:オーナーに経験値の処理
    }

    public function onMedicineBoxEffect(MedicineBoxEffectOnEvent $event): void {
        $receiver = $event->getReceiver();
        $receiver->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 2, 1));
    }

    public function onFlareBoxEffect(FlareBoxEffectOnEvent $event): void {
        $owner = $event->getOwner();
        $receiver = $event->getReceiver();
        $receiverData = TeamGameSystem::getPlayerData($receiver);
        if ($receiverData->getGameId() === null) return;
        ShowPrivateNameTagToParticipantsPMMPService::execute($receiver, $receiverData->getGameId());
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $i) use ($receiver, $receiverData) {
            ShowPrivateNameTagToAllyPMMPService::execute($receiver, $receiverData->getTeamId());
        }), 20 * 3);

        $receiver->sendTip("スポットされました！３秒間相手に居場所がばれます！");
        //TODO:オーナーに経験値の処理
    }

    public function onStopBox(BoxStopEvent $event) {
        $box = $event->getBox();
        $owner = $event->getOwner();

        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($owner, $box) {
            $status = PlayerStatusDAO::get($owner->getName());
            $boxes = $status->getMilitaryDepartment()->getBoxes();
            if (in_array($box, $boxes)) {
                $owner->getInventory()->addItem(BoxItem::fromBox($box));
            }
        }), 20 * 15);
    }
}