<?php


namespace mine_deep_rock\pmmp\listener;


use box_system\pmmp\entities\BoxEntity;
use box_system\pmmp\events\AmmoBoxEffectOnEvent;
use box_system\pmmp\events\BoxStopEvent;
use box_system\pmmp\events\FlareBoxEffectOnEvent;
use box_system\pmmp\events\MedicineBoxEffectOnEvent;
use box_system\pmmp\items\BoxItem;
use gun_system\GunSystem;
use gun_system\model\Gun;
use gun_system\pmmp\item\ItemGun;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\pmmp\service\ShowPrivateNameTagToAllyPMMPService;
use mine_deep_rock\pmmp\service\ShowPrivateNameTagToParticipantsPMMPService;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
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
        if ($receiver->getGamemode() !== Player::ADVENTURE) return;

        $receiverData = TeamGameSystem::getPlayerData($receiver);
        $ownerData = TeamGameSystem::getPlayerData($owner);

        if ($receiverData->getGameId() === null || $ownerData->getGameId() === null) return;
        if (!$receiverData->getTeamId()->equals($ownerData->getTeamId())) return;

        $itemGun = $receiver->getInventory()->getItem(0);
        $itemSubGun = $receiver->getInventory()->getItem(1);
        if ($itemGun instanceof ItemGun && $itemSubGun instanceof ItemGun) {
            $gun = $itemGun->getGun();
            $subGun = $itemSubGun->getGun();

            $this->giveAmmo($owner, $receiver, $gun, 0);
            $this->giveAmmo($owner, $receiver, $subGun, 1);
            //TODO:オーナーに経験値の処理
        }
    }

    private function giveAmmo(Player $owner, Player $receiver, Gun $gun, int $slot) {
        $remain = $gun->getInitialAmmo() - $gun->getMagazineData()->getCurrentAmmo();
        if ($remain === 0) return;

        if ($remain >= $gun->getMagazineData()->getCapacity()) {
            GunSystem::giveAmmo($receiver, $slot, $gun->getMagazineData()->getCapacity());
        } else {
            GunSystem::giveAmmo($receiver, $slot, $remain);
        }

        $receiver->sendTip("{$owner->getName()}から弾薬を供給");
        $owner->sendTip("{$receiver->getName()}に弾薬を供給");
    }

    public function onMedicineBoxEffect(MedicineBoxEffectOnEvent $event): void {
        $receiver = $event->getReceiver();
        $owner = $event->getOwner();
        if ($receiver->getGamemode() !== Player::ADVENTURE) return;

        $receiverData = TeamGameSystem::getPlayerData($receiver);
        $ownerData = TeamGameSystem::getPlayerData($owner);

        if ($receiverData->getGameId() === null || $ownerData->getGameId() === null) return;
        if (!$receiverData->getTeamId()->equals($ownerData->getTeamId())) return;

        $receiver->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 2, 2));
    }

    public function onFlareBoxEffect(FlareBoxEffectOnEvent $event): void {
        $owner = $event->getOwner();
        $receiver = $event->getReceiver();
        if ($receiver->getGamemode() !== Player::ADVENTURE) return;

        $receiverData = TeamGameSystem::getPlayerData($receiver);
        $ownerData = TeamGameSystem::getPlayerData($owner);

        if ($receiverData->getGameId() === null || $ownerData->getGameId() === null) return;
        if ($receiverData->getTeamId()->equals($ownerData->getTeamId())) return;

        ShowPrivateNameTagToParticipantsPMMPService::execute($receiver, $receiverData->getGameId());
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $i) use ($receiver) : void {
            ShowPrivateNameTagToAllyPMMPService::execute($receiver);
        }), 20 * 3);

        $receiver->sendTip("スポットされました！３秒間相手に居場所がばれます！");
        //TODO:オーナーに経験値の処理
    }

    public function onStopBox(BoxStopEvent $event) {
        $box = $event->getBox();
        $owner = $event->getOwner();

        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($owner, $box): void {
            $playerData = TeamGameSystem::getPlayerData($owner);
            if ($playerData->getGameId() === null) return;

            $status = PlayerStatusDAO::get($owner->getName());
            $boxes = $status->getMilitaryDepartment()->getBoxes();
            if (in_array($box, $boxes)) {
                $boxItem = BoxItem::fromBox($box);

                if ($owner->getGamemode() === Player::SPECTATOR) return;
                if (!$owner->getInventory()->contains($boxItem)) {
                    $owner->getInventory()->addItem($boxItem);
                }
            }
        }), 20 * 15);
    }

    public function onDamaged(EntityDamageByEntityEvent $event) {
        $victim = $event->getEntity();
        $attacker = $event->getDamager();
        if ($attacker instanceof Player && $victim instanceof BoxEntity) {
            $owner = $victim->getOwner();

            $attackerData = TeamGameSystem::getPlayerData($attacker);
            $ownerData = TeamGameSystem::getPlayerData($owner);
            if ($attackerData->getGameId() === null || $ownerData->getGameId() === null) return;
            if ($attackerData->getTeamId()->equals($ownerData->getTeamId())) $event->setCancelled();
        }
    }
}