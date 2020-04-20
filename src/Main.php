<?php

use gun_system\GunSystemClient;
use gun_system\models\BulletId;
use gun_system\pmmp\command\GunCommand;
use gun_system\pmmp\items\bullet\ItemAssaultRifleBullet;
use gun_system\pmmp\items\bullet\ItemHandGunBullet;
use gun_system\pmmp\items\bullet\ItemShotgunBullet;
use gun_system\pmmp\items\bullet\ItemSniperRifleBullet;
use gun_system\pmmp\items\bullet\ItemSubMachineGunBullet;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Egg;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\plugin\PluginBase;
use team_system\pmmp\command\TeamCommand;
use team_system\services\MemberService;
use team_system\services\TeamService;
use team_system\TeamSystemClient;
use team_system\TeamSystemNotifier;

class Main extends PluginBase implements Listener
{
    private $teamSystemClient;
    private $gunSystemClient;

    function onEnable() {
        $this->teamSystemClient = new TeamSystemClient(new TeamService(), new MemberService(), new TeamSystemNotifier(function () {
            //TODO:
        }));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("team", new TeamCommand($this, $this->teamSystemClient));
        $this->getServer()->getCommandMap()->register("gun", new GunCommand($this, $this->getScheduler()));

        $this->gunSystemClient = new GunSystemClient();

        ItemFactory::registerItem(new ItemAssaultRifleBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::ASSAULT_RIFLE));

        ItemFactory::registerItem(new ItemHandGunBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::HAND_GUN));

        ItemFactory::registerItem(new ItemShotgunBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::SHOTGUN));

        ItemFactory::registerItem(new ItemSniperRifleBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::SNIPER_RIFLE));

        ItemFactory::registerItem(new ItemSubMachineGunBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::SMG));

        Entity::registerEntity(\gun_system\pmmp\entity\Egg::class, true, ['Egg', 'minecraft:egg']);
    }


    //GunSystem
    //空中を右クリック,Tapで一発だけ射撃
    public function tryShootingOnce(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $this->gunSystemClient->tryShootingOnce($item, $this->getScheduler());
            }
        }
    }

    //空中を右クリックwin10,tap長押しで射撃
    public function tryShooting(PlayerInteractEvent $event) {
        if (in_array($event->getAction(), [PlayerInteractEvent::RIGHT_CLICK_AIR])) {
            $player = $event->getPlayer();
            $item = $event->getItem();
            $this->gunSystemClient->tryShooting($item);
        }
    }

    //銃を捨てるでリロード
    public function tryReloading(InventoryTransactionEvent $event): void {
        $player = $event->getTransaction()->getSource();
        $actions = array_values($event->getTransaction()->getActions());

        $dropItemActions = array_values(array_filter($actions, function ($item) {
            return $item instanceof DropItemAction;
        }));
        $slotChangeActions = array_values(array_filter($actions, function ($item) {
            return $item instanceof SlotChangeAction;
        }));


        if (count($dropItemActions) !== 0 && count($slotChangeActions) !== 0) {
            $slotChangeAction = $slotChangeActions[0];
            $inventory = $slotChangeAction->getInventory();
            if ($inventory instanceof PlayerInventory) {
                $item = $inventory->getItemInHand();
                if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
                    $this->gunSystemClient->tryReloading($item);
                    $event->setCancelled();
                }
            }
        }
    }

    public function onBulletHit(ProjectileHitEntityEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof \gun_system\pmmp\entity\Egg) {
            $this->gunSystemClient->sendDamageByShooting($entity->getOwningEntity(), $event->getEntityHit());
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event) {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
            if ($player->isSneaking()) {
                $player->removeEffect(Effect::SLOWNESS);
            } else {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), null, 5));
            }
        }
    }

    //TeamSystem
    public function onJoin(PlayerJoinEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->teamSystemClient->onJoin($playerName);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->teamSystemClient->onLeave($playerName);

    }
}