<?php

use game_system\GameSystemListener;
use game_system\pmmp\command\GameCommand;
use game_system\pmmp\command\StateCommand;
use game_system\pmmp\Entity\AmmoBoxEntity;
use game_system\pmmp\items\MilitaryDepartmentSelectItem;
use game_system\pmmp\items\SpawnAmmoBoxItem;
use game_system\pmmp\items\SpawnItem;
use game_system\pmmp\items\SubWeaponSelectItem;
use game_system\pmmp\items\WeaponPurchaseItem;
use game_system\pmmp\items\WeaponSelectItem;
use gun_system\EffectiveRangeLoader;
use gun_system\GunSystemListener;
use gun_system\models\BulletId;
use gun_system\pmmp\command\GunCommand;
use gun_system\pmmp\items\bullet\ItemAssaultRifleBullet;
use gun_system\pmmp\items\bullet\ItemHandGunBullet;
use gun_system\pmmp\items\bullet\ItemLightMachineGunBullet;
use gun_system\pmmp\items\bullet\ItemRevolverBullet;
use gun_system\pmmp\items\bullet\ItemShotgunBullet;
use gun_system\pmmp\items\bullet\ItemSniperRifleBullet;
use gun_system\pmmp\items\bullet\ItemSubMachineGunBullet;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
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
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    private $gameSystemListener;
    private $gunSystemClient;

    function onEnable() {
        $effectiveRangeLoader = new EffectiveRangeLoader();
        $effectiveRangeLoader->loadAll();

        $this->gunSystemClient = new GunSystemListener();
        $this->gameSystemListener = new GameSystemListener($this->getScheduler());

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("gun", new GunCommand($this, $this->getScheduler(), $this->getServer()));
        $this->getServer()->getCommandMap()->register("game", new GameCommand($this, $this->gameSystemListener, $this->getScheduler()));
        $this->getServer()->getCommandMap()->register("state", new StateCommand($this, $this->gameSystemListener));


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

        ItemFactory::registerItem(new ItemLightMachineGunBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::LMG));

        ItemFactory::registerItem(new ItemRevolverBullet(), true);
        Item::addCreativeItem(Item::get(BulletId::REVOLVER));

        Entity::registerEntity(\game_system\pmmp\Entity\Egg::class, true, ['Egg', 'minecraft:egg']);


        ItemFactory::registerItem(new WeaponSelectItem(), true);
        Item::addCreativeItem(Item::get(WeaponSelectItem::ITEM_ID));

        ItemFactory::registerItem(new SubWeaponSelectItem(), true);
        Item::addCreativeItem(Item::get(SubWeaponSelectItem::ITEM_ID));

        ItemFactory::registerItem(new WeaponPurchaseItem(), true);
        Item::addCreativeItem(Item::get(WeaponPurchaseItem::ITEM_ID));

        ItemFactory::registerItem(new SpawnItem(), true);
        Item::addCreativeItem(Item::get(SpawnItem::ITEM_ID));

        ItemFactory::registerItem(new SpawnAmmoBoxItem(), true);
        Item::addCreativeItem(Item::get(SpawnAmmoBoxItem::ITEM_ID));

        Entity::registerEntity(AmmoBoxEntity::class, true, ['AmmoBox']);

        $this->gameSystemListener->initGame(new \game_system\model\map\ApocalypticCity());
    }


    //GunSystem
    //空中を右クリック,Tapで一発だけ射撃
    public function tryShootingOnce(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $this->gunSystemClient->tryShootingOnce($player, $item);
            }
        }
    }

    //空中を右クリックwin10,tap長押しで射撃
    public function tryShooting(PlayerInteractEvent $event) {
        if (in_array($event->getAction(), [PlayerInteractEvent::RIGHT_CLICK_AIR])) {
            $player = $event->getPlayer();
            $item = $event->getItem();
            $this->gunSystemClient->tryShooting($player, $item);
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
                $effectLevel = $item->getInterpreter()->getScope()->getMagnification()->getValue();
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), null, $effectLevel, false));
            }
        }
    }

    public function onBulletHit(ProjectileHitEntityEvent $event) {
        $entity = $event->getEntity();
        $attacker = $entity->getOwningEntity();
        if (!($entity instanceof AmmoBoxEntity)) {
            if ($entity instanceof \game_system\pmmp\Entity\Egg && $attacker instanceof Human) {
                $item = $attacker->getInventory()->getItemInHand();
                $damage = $this->gunSystemClient->receivedDamage($attacker, $event->getEntityHit());
                $this->gameSystemListener->onReceivedDamage($attacker, $event->getEntityHit(), $item->getCustomName(), $damage);
            }
        }
    }

    //GameSystemListener
    public function onTapByWeaponSelectItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            if ($player->getInventory()->getItemInHand()->getId() === WeaponSelectItem::ITEM_ID) {
                $this->gameSystemListener->displayWeaponSelectForm($player);
            }
        }
    }

    public function onTapBySubWeaponSelectItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            if ($player->getInventory()->getItemInHand()->getId() === SubWeaponSelectItem::ITEM_ID) {
                $this->gameSystemListener->displaySubWeaponSelectForm($player);
            }
        }
    }

    public function onTapByWeaponPurchaseItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            if ($player->getInventory()->getItemInHand()->getId() === WeaponPurchaseItem::ITEM_ID) {
                $this->gameSystemListener->displayWeaponPurchaseForm($player);
            }
        }
    }

    public function onTapBySpawnItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            if ($player->getInventory()->getItemInHand()->getId() === SpawnItem::ITEM_ID) {
                $this->gameSystemListener->spawnOnTeamDeath($player->getName());
            }
        }
    }

    public function onTapBySpawnAmmoBoxItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            if ($player->getInventory()->getItemInHand()->getId() === SpawnAmmoBoxItem::ITEM_ID) {
                $this->gameSystemListener->spawnAmmoBox($player);
            }
        }
    }

    public function onTapByMilitaryDepartmentSelectItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            if ($player->getInventory()->getItemInHand()->getId() === MilitaryDepartmentSelectItem::ITEM_ID) {
                $this->gameSystemListener->displayMilitaryDepartmentSelectForm($player);
            }
        }
    }

    public function onTapByForTapUser(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                switch ($item->getId()) {
                    case WeaponSelectItem::ITEM_ID:
                        $this->gameSystemListener->displayWeaponSelectForm($player);
                        break;
                    case SubWeaponSelectItem::ITEM_ID:
                        $this->gameSystemListener->displaySubWeaponSelectForm($player);
                        break;
                    case WeaponPurchaseItem::ITEM_ID:
                        $this->gameSystemListener->displayWeaponPurchaseForm($player);
                        break;
                    case SpawnItem::ITEM_ID:
                        $this->gameSystemListener->spawnOnTeamDeath($player->getName());
                        break;
                    case SpawnAmmoBoxItem::ITEM_ID:
                        $this->gameSystemListener->spawnAmmoBox($player);
                        break;
                    case MilitaryDepartmentSelectItem::ITEM_ID:
                        $this->gameSystemListener->displayMilitaryDepartmentSelectForm($player);
                        break;
                }
            }
        }
    }

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof Human) {
            $event->setCancelled();
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->gameSystemListener->userLogin($player->getName());
        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->gameSystemListener->quitGame($playerName);
    }
}