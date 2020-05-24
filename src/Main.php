<?php

use game_system\GameSystemBinder;
use game_system\listener\TwoTeamGameListener;
use game_system\listener\UsersListener;
use game_system\listener\WeaponListener;
use game_system\pmmp\command\GameCommand;
use game_system\pmmp\command\NPCCommand;
use game_system\pmmp\command\RankingCommand;
use game_system\pmmp\command\StateCommand;
use game_system\pmmp\command\WorldCommand;
use game_system\pmmp\Entity\AmmoBoxEntity;
use game_system\pmmp\Entity\BoxEntity;
use game_system\pmmp\Entity\CadaverEntity;
use game_system\pmmp\Entity\FlagEntity;
use game_system\pmmp\Entity\FlameBottleEntity;
use game_system\pmmp\Entity\FlareBoxEntity;
use game_system\pmmp\Entity\FragGrenadeEntity;
use game_system\pmmp\Entity\GadgetEntity;
use game_system\pmmp\Entity\GameMasterNPC;
use game_system\pmmp\Entity\GunDealerNPC;
use game_system\pmmp\Entity\MedicineBoxEntity;
use game_system\pmmp\Entity\SandbagEntity;
use game_system\pmmp\Entity\SmokeGrenadeEntity;
use game_system\pmmp\Entity\SpawnBeaconEntity;
use game_system\pmmp\Entity\TargetNPC;
use game_system\pmmp\Entity\TrialGunDealerNPC;
use game_system\pmmp\items\FlameBottleItem;
use game_system\pmmp\items\FragGrenadeItem;
use game_system\pmmp\items\SandbagItem;
use game_system\pmmp\items\SmokeGrenadeItem;
use game_system\pmmp\items\SpawnBeaconItem;
use game_system\pmmp\items\SpawnFlareBoxItem;
use game_system\pmmp\items\MilitaryDepartmentSelectItem;
use game_system\pmmp\items\SpawnAmmoBoxItem;
use game_system\pmmp\items\SpawnItem;
use game_system\pmmp\items\SpawnMedicineBoxItem;
use game_system\pmmp\items\SubWeaponSelectItem;
use game_system\pmmp\items\WeaponSelectItem;
use game_system\pmmp\WorldController;
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
use gun_system\pmmp\items\ItemSniperRifle;
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
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase implements Listener
{
    private $gunSystemClient;
    /**
     * @var TwoTeamGameListener
     */
    private $gameListener;
    /**
     * @var UsersListener
     */
    private $usersListener;
    /**
     * @var WeaponListener
     */
    private $weaponsListener;

    function onEnable() {
        $effectiveRangeLoader = new EffectiveRangeLoader();
        $effectiveRangeLoader->loadAll();

        $this->gunSystemClient = new GunSystemListener();
        $gameSystemBinder = new GameSystemBinder($this->getScheduler());
        $this->gameListener = $gameSystemBinder->getGameListener();
        $this->usersListener = $gameSystemBinder->getUsersListener();
        $this->weaponsListener = $gameSystemBinder->getWeaponListener();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("gun", new GunCommand($this, $this->getScheduler(), $this->getServer()));
        $this->getServer()->getCommandMap()->register("game", new GameCommand($this, $this->gameListener, $this->getScheduler()));
        $this->getServer()->getCommandMap()->register("state", new StateCommand($this, $this->usersListener));
        $this->getServer()->getCommandMap()->register("world", new WorldCommand($this));
        $this->getServer()->getCommandMap()->register("npc", new NPCCommand($this));
        $this->getServer()->getCommandMap()->register("ranking", new RankingCommand($this, $this->weaponsListener));


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

        ItemFactory::registerItem(new SpawnItem(), true);
        Item::addCreativeItem(Item::get(SpawnItem::ITEM_ID));

        ItemFactory::registerItem(new SpawnAmmoBoxItem(), true);
        Item::addCreativeItem(Item::get(SpawnAmmoBoxItem::ITEM_ID));

        ItemFactory::registerItem(new SpawnMedicineBoxItem(), true);
        Item::addCreativeItem(Item::get(SpawnMedicineBoxItem::ITEM_ID));

        ItemFactory::registerItem(new SpawnFlareBoxItem(), true);
        Item::addCreativeItem(Item::get(SpawnFlareBoxItem::ITEM_ID));

        Entity::registerEntity(AmmoBoxEntity::class, true, ['AmmoBox']);
        Entity::registerEntity(MedicineBoxEntity::class, true, ['MedicineBox']);
        Entity::registerEntity(FlareBoxEntity::class, true, ['FlareBox']);

        Entity::registerEntity(GunDealerNPC::class, true, ['GunDealer']);
        Entity::registerEntity(GameMasterNPC::class, true, ['GameMaster']);
        Entity::registerEntity(TargetNPC::class, true, ['Target']);
        Entity::registerEntity(TrialGunDealerNPC::class, true, ['TrialGunDealer']);

        Entity::registerEntity(FlagEntity::class, true, ['Flag']);

        Entity::registerEntity(SmokeGrenadeEntity::class, true, ['SmokeGrenade']);
        Entity::registerEntity(FragGrenadeEntity::class, true, ['FragGrenade']);
        Entity::registerEntity(FlameBottleEntity::class, true, ['FlameBottle']);

        Entity::registerEntity(SandbagEntity::class, true, ['Sandbag']);
        Entity::registerEntity(SpawnBeaconEntity::class, true, ['SpawnBeacon']);

        Entity::registerEntity(CadaverEntity::class, true, ['CadaverEntity']);

        $this->gameListener->initGame(\game_system\model\GameType::TeamDomination());
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

    //エンティティをなぐるで一発だけ射撃
    public function tryShootingByTapPlayer(EntityDamageEvent $event) {
        if ($event instanceof EntityDamageByEntityEvent) {
            $player = $event->getDamager();
            if ($player instanceof Player
                && $event->getCause() === EntityDamageEvent::MODIFIER_ARMOR) {
                $item = $player->getInventory()->getItemInHand();
                $this->gunSystemClient->tryShootingOnce($player, $item);
            }
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
            $event->setCancelled();
        }
    }

    //アイテム持ち替えでリロードキャンセル
    public function cancelReloading(\pocketmine\event\player\PlayerItemHeldEvent $event) {
        $player = $event->getPlayer();
        $currentItem = $player->getInventory()->getItemInHand();
        $nextItem = $event->getItem();
        if ($currentItem instanceof \gun_system\pmmp\items\ItemGun) {
            if ($currentItem->getName() === $nextItem->getName()) {
                $this->gunSystemClient->tryReloading($currentItem);
            } else {
                $this->gunSystemClient->tryCancelReloading($currentItem);
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
            $player->getArmorInventory()->removeItem(ItemFactory::get(Item::PUMPKIN));
            $player->removeEffect(Effect::SLOWNESS);
        } else {
            if (is_subclass_of($item, "gun_system\pmmp\items\ItemGun")) {
                $effectLevel = $item->getInterpreter()->getScope()->getMagnification()->getValue();
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), null, $effectLevel, false));
                if ($item instanceof ItemSniperRifle) {
                    $player->getArmorInventory()->setHelmet(ItemFactory::get(Item::PUMPKIN));
                }
            }
        }
    }

    public function onBulletHit(ProjectileHitEntityEvent $event): void {
        $bullet = $event->getEntity();
        $victim = $event->getEntityHit();
        $attacker = $bullet->getOwningEntity();

        if ($attacker instanceof Player && $victim instanceof GadgetEntity) {
            $this->gameListener->onGadgetHitBullet($attacker, $victim);
            return;
        }

        if ($bullet instanceof \game_system\pmmp\Entity\Egg && $victim instanceof TargetNPC) {
            $damage = $this->gunSystemClient->receivedDamage($attacker, $victim);
            if ($attacker instanceof Player) {
                $health = $victim->getHealth() - $damage;
                $attacker->sendMessage(strval($damage));
                if ($health <= 0) {
                    $nbt = new CompoundTag('', [
                        'Pos' => new ListTag('Pos', [
                            new DoubleTag('', $victim->getX()),
                            new DoubleTag('', $victim->getY() + 0.5),
                            new DoubleTag('', $victim->getZ())
                        ]),
                        'Motion' => new ListTag('Motion', [
                            new DoubleTag('', 0),
                            new DoubleTag('', 0),
                            new DoubleTag('', 0)
                        ]),
                        'Rotation' => new ListTag('Rotation', [
                            new FloatTag("", $victim->getYaw()),
                            new FloatTag("", $victim->getPitch())
                        ]),
                    ]);
                    $target = new TargetNPC($victim->getLevel(), $nbt);
                    $victim->setHealth($health);
                    $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($target): void {
                        $target->spawnToAll();
                    }), 20 * 3);
                } else {
                    $victim->setHealth($health);
                }
            }
            return;
        }

        if ($bullet instanceof \game_system\pmmp\Entity\Egg && $victim instanceof Player) {
            $item = $attacker->getInventory()->getItemInHand();
            $damage = $this->gunSystemClient->receivedDamage($attacker, $victim);
            $this->gameListener->onReceivedDamage($attacker, $victim, $item->getCustomName(), $damage);
            return;
        }
    }

    //GameSystemListener
    public function onTapByItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            $item = $player->getInventory()->getItemInHand();
            switch ($item->getId()) {
                case WeaponSelectItem::ITEM_ID:
                    $this->weaponsListener->displayWeaponSelectForm($player);
                    break;
                case SubWeaponSelectItem::ITEM_ID:
                    $this->weaponsListener->displaySubWeaponSelectForm($player);
                    break;
                case SpawnItem::ITEM_ID:
                    $this->gameListener->spawnOnTeamDeath($player->getName());
                    break;
                case SpawnAmmoBoxItem::ITEM_ID:
                    $this->gameListener->spawnAmmoBox($player);
                    break;
                case SpawnMedicineBoxItem::ITEM_ID:
                    $this->gameListener->spawnMedicineBox($player);
                    break;
                case MilitaryDepartmentSelectItem::ITEM_ID:
                    $this->usersListener->displayMilitaryDepartmentSelectForm($player);
                    break;
                case SpawnFlareBoxItem::ITEM_ID:
                    $this->gameListener->spawnFlareBox($player);
                    break;
                case FragGrenadeItem::ITEM_ID:
                    $this->gameListener->spawnFragGrenadeEntity($player);
                    break;
                case SmokeGrenadeItem::ITEM_ID:
                    $this->gameListener->spawnSmokeGrenadeEntity($player);
                    break;
                case FlameBottleItem::ITEM_ID:
                    $this->gameListener->spawnFlameBottleEntity($player);
                    break;
                case SpawnBeaconItem::ITEM_ID:
                    $this->gameListener->spawnSpawnBeacon($player);
                    break;
                case SandbagItem::ITEM_ID:
                    $this->gameListener->spawnSandbag($player);
                    break;
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
                        $this->weaponsListener->displayWeaponSelectForm($player);
                        break;
                    case SubWeaponSelectItem::ITEM_ID:
                        $this->weaponsListener->displaySubWeaponSelectForm($player);
                        break;
                    case SpawnItem::ITEM_ID:
                        $this->gameListener->spawnOnTeamDeath($player->getName());
                        break;
                    case SpawnAmmoBoxItem::ITEM_ID:
                        $this->gameListener->spawnAmmoBox($player);
                        break;
                    case SpawnMedicineBoxItem::ITEM_ID:
                        $this->gameListener->spawnMedicineBox($player);
                        break;
                    case MilitaryDepartmentSelectItem::ITEM_ID:
                        $this->usersListener->displayMilitaryDepartmentSelectForm($player);
                        break;
                    case SpawnFlareBoxItem::ITEM_ID:
                        $this->gameListener->spawnFlareBox($player);
                        break;
                    case FragGrenadeItem::ITEM_ID:
                        $this->gameListener->spawnFragGrenadeEntity($player);
                        break;
                    case SmokeGrenadeItem::ITEM_ID:
                        $this->gameListener->spawnSmokeGrenadeEntity($player);
                        break;
                    case FlameBottleItem::ITEM_ID:
                        $this->gameListener->spawnFlameBottleEntity($player);
                        break;
                    case SpawnBeaconItem::ITEM_ID:
                        $this->gameListener->spawnSpawnBeacon($player);
                        break;
                    case SandbagItem::ITEM_ID:
                        $this->gameListener->spawnSandbag($player);
                        break;
                }
            }
        }
    }

    public function cancelDamage(EntityDamageEvent $event) {
        if ($event->getEntity() instanceof Human) {
            $event->setCancelled();
        }
    }

    public function resuscitate(EntityDamageEvent $event) {
        if ($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            $attacker = $event->getDamager();

            if ($event->getCause() === $event::CAUSE_PROJECTILE) return;

            if ($attacker instanceof Player && $entity instanceof \game_system\pmmp\Entity\CadaverEntity) {
                $this->gameListener->resuscitate($attacker, $entity);
            }
        }
    }

    public function tapGadget(EntityDamageEvent $event) {
        if ($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            $attacker = $event->getDamager();

            if ($event->getCause() === $event::CAUSE_PROJECTILE) return;

            if ($attacker instanceof Player) {
                if ($entity instanceof SandbagEntity) {
                    if ($entity->getOwnerName() === $attacker->getName()) {
                        $entity->kill();
                        if (!$attacker->getInventory()->contains(new SandbagItem()))
                            $attacker->getInventory()->addItem(new SandbagItem());
                    }
                }
                if ($entity instanceof SpawnBeaconEntity) {
                    if ($entity->getOwnerName() === $attacker->getName()) {
                        $entity->kill();
                        if (!$attacker->getInventory()->contains(new SpawnBeaconItem()))
                            $attacker->getInventory()->addItem(new SpawnBeaconItem());
                    }
                }
            }
        }
    }

    public function tapDealer(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if ($event instanceof EntityDamageByEntityEvent) {
            $player = $event->getDamager();
            if ($player instanceof Player) {
                if ($entity instanceof GunDealerNPC) {
                    $this->weaponsListener->displayWeaponPurchaseForm($player);
                } else if ($entity instanceof GameMasterNPC) {
                    $this->gameListener->joinGame($player);
                } else if ($entity instanceof TrialGunDealerNPC) {
                    $this->weaponsListener->displayTrialWeaponSelectForm($player);
                }
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $this->usersListener->userLogin($player);
        $this->gameListener->displayParticipantCount();
        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->gameListener->quitGame($playerName);
    }

    public function cancelMoving(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $contain = $player->getInventory()->contains(new SpawnItem());
        if ($contain) {
            $from = $event->getFrom();
            $to = $event->getTo();
            //スペクテイターでエイム動かしたときの値が0.0010000000000003
            if ($from->distance($to) < 0.0011) return;
            $event->setCancelled();
        }
    }

    public function cancelExhausting(\pocketmine\event\player\PlayerExhaustEvent $event) {
        $event->setCancelled();
    }
}