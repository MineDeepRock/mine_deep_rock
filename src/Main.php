<?php

use game_system\GameSystemClient;
use game_system\GameSystemListener;
use game_system\pmmp\command\GameCommand;
use gun_system\GunSystemClient;
use gun_system\models\BulletId;
use gun_system\pmmp\command\GunCommand;
use gun_system\pmmp\items\bullet\ItemAssaultRifleBullet;
use gun_system\pmmp\items\bullet\ItemBuckShotBullet;
use gun_system\pmmp\items\bullet\ItemHandGunBullet;
use gun_system\pmmp\items\bullet\ItemLightMachineGunBullet;
use gun_system\pmmp\items\bullet\ItemRevolverBullet;
use gun_system\pmmp\items\bullet\ItemSlugBullet;
use gun_system\pmmp\items\bullet\ItemSniperRifleBullet;
use gun_system\pmmp\items\bullet\ItemSubMachineGunBullet;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
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
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    private $gameSystemListener;
    private $gunSystemClient;

    function onEnable() {
        $this->gunSystemClient = new GunSystemClient();
        $this->gameSystemListener = new GameSystemListener();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("gun", new GunCommand($this, $this->getScheduler(), $this->getServer()));
        $this->getServer()->getCommandMap()->register("game", new GameCommand($this, $this->gameSystemListener, $this->getScheduler()));


        ItemFactory::registerItem(new ItemAssaultRifleBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::ASSAULT_RIFLE));

        ItemFactory::registerItem(new ItemHandGunBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::HAND_GUN));

        ItemFactory::registerItem(new ItemBuckShotBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::BUCK_SHOT));
        ItemFactory::registerItem(new ItemSlugBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::SLUG));

        ItemFactory::registerItem(new ItemSniperRifleBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::SNIPER_RIFLE));

        ItemFactory::registerItem(new ItemSubMachineGunBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::SMG));

        ItemFactory::registerItem(new ItemLightMachineGunBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::LMG));

        ItemFactory::registerItem(new ItemRevolverBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::REVOLVER));

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
                $this->gunSystemClient->tryShootingOnce($player,$item);
            }
        }
    }

    //空中を右クリックwin10,tap長押しで射撃
    public function tryShooting(PlayerInteractEvent $event) {
        if (in_array($event->getAction(), [PlayerInteractEvent::RIGHT_CLICK_AIR])) {
            $player = $event->getPlayer();
            $item = $event->getItem();
            $this->gunSystemClient->tryShooting($player,$item);
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

    //プレイヤーから半径3ブロック未満の地面tapでリロード
    public function tryReloadingForTap(PlayerInteractEvent $event) {
        if (in_array($event->getAction(), [PlayerInteractEvent::RIGHT_CLICK_BLOCK])) {
            $player = $event->getPlayer();
            $touchedBlockPos = new Vector3(
                $event->getBlock()->getX(),
                $event->getBlock()->getY(),
                $event->getBlock()->getZ()
            );
            if ($player->getPosition()->distance($touchedBlockPos) < 3) {
                $item = $event->getItem();
                $this->gunSystemClient->tryReloading($item);
            }
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event) {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($player->isSneaking()) {
            $player->removeEffect(Effect::SLOWNESS);
        } else {
            if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
                $effectLevel = $item->getGunData()->getScope()->getMagnification()->getValue();
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), null, $effectLevel));
            }
        }
    }

    public function onBulletHit(ProjectileHitEntityEvent $event) {
        $entity = $event->getEntity();
        $attacker = $entity->getOwningEntity();
        if ($entity instanceof \gun_system\pmmp\entity\Egg && $attacker instanceof Human) {
            $item = $attacker->getInventory()->getItemInHand();
            $damage = $this->gunSystemClient->receivedDamage($attacker, $event->getEntityHit());
            $this->gameSystemListener->onReceivedDamage($attacker, $event->getEntityHit(), $item->getCustomName(), $damage);
        }
    }

    //GameSystemListener
    public function onTapWeaponSelectBlock(PlayerInteractEvent $event){
        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            if ($event->getBlock()->getId() === 41) {
                $this->gameSystemListener->selectWeapon($player);
            }
        }
    }

    public function onDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        if ($entity instanceof Human) {
            $event->setCancelled();
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->gameSystemListener->userLogin($playerName);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->gameSystemListener->quitGame($playerName);
    }
}