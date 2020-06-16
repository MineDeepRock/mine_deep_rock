<?php


namespace mine_deep_rock\listeners;


use box_system\pmmp\events\AmmoBoxEffectOnEvent;
use box_system\pmmp\events\BoxStopEvent;
use box_system\pmmp\events\FlareBoxEffectOnEvent;
use box_system\pmmp\events\MedicineBoxEffectOnEvent;
use gun_system\GunSystem;
use military_department_system\models\GadgetType;
use mine_deep_rock\controllers\TwoTeamNameTagController;
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

    //TODO:Game終了時、開始のイベントを受け取り更新する。いずれは複数形
    static private $game;

    public function __construct(Server $server, TaskScheduler $scheduler) {
        $this->server = $server;
        $this->scheduler = $scheduler;
    }

    /**
     * @param mixed $game
     */
    public static function setGame($game): void {
        self::$game = $game;
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
        if (self::$game === null) return;

        $receiver = $event->getReceiver();
        $receiverData = TeamSystem::getPlayerData($receiver->getName());
        $ownerData = TeamSystem::getPlayerData($event->getOwner()->getName());
        if ($receiverData->getBelongTeamId() === null || $ownerData->getBelongTeamId() === null) return;
        if ($receiverData->getBelongTeamId()->equal($ownerData->getBelongTeamId())) return;

        TwoTeamNameTagController::showToParticipants($receiver, self::$game);
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $i) use ($receiver, $receiverData): void {
            if ($receiver->isOnline()) {
                TwoTeamNameTagController::showToAlly($receiver, self::$game);
            }
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