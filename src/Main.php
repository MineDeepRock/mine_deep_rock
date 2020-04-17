<?php

use gun_system\GunSystemClient;
use gun_system\models\BulletId;
use gun_system\pmmp\command\GunCommand;
use gun_system\pmmp\items\bullet\ItemAssaultRifleBullet;
use gun_system\pmmp\items\bullet\ItemHandGunBullet;
use gun_system\pmmp\items\bullet\ItemShotgunBullet;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\projectile\Egg;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
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
    }


    //GunSystem
    //public function onTouchAir(DataPacketReceiveEvent $event) {
    //    $packet = $event->getPacket();
    //    if ($packet instanceof LevelSoundEventPacket) {
    //        if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
    //            $player = $event->getPlayer();
    //            $item = $event->getPlayer()->getInventory()->getItemInHand();
    //            $this->gunSystemClient->tryShooting($item, $player,$this->getScheduler());
    //        }
    //    }
    //}

    public function onTouch(PlayerInteractEvent $event) {
        if (in_array($event->getAction(), [PlayerInteractEvent::RIGHT_CLICK_AIR, PlayerInteractEvent::RIGHT_CLICK_BLOCK])) {
            $player = $event->getPlayer();
            $item = $event->getItem();
            $this->gunSystemClient->tryShooting($item, $player);
        }
    }

    public function onBulletHit(ProjectileHitEntityEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof Egg) {
            $this->gunSystemClient->sendDamageByShooting($entity->getOwningEntity(), $event->getEntityHit());
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event) {
        $player = $event->getPlayer();

        if ($player->isSneaking()) {
            $player->removeEffect(Effect::SLOWNESS);
        } else {
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), null, 5));
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