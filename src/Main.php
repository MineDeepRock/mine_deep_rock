<?php

use gun_system\GunSystemClient;
use gun_system\models\GunId;
use gun_system\pmmp\items\assault_rifle\ItemM1Grand;
use gun_system\pmmp\items\hand_gun\ItemDesertEagle;
use gun_system\pmmp\items\hand_gun\ItemM1911;
use gun_system\pmmp\items\hand_gun\ItemP08;
use gun_system\pmmp\items\shotgun\ItemM1897;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\projectile\Egg;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
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

        $this->gunSystemClient = new GunSystemClient();

        ItemFactory::registerItem(new ItemDesertEagle($this->getScheduler()), true);
        Item::addCreativeItem(Item::get(GunId::DESERT_EAGLE));

        ItemFactory::registerItem(new ItemM1911($this->getScheduler()), true);
        Item::addCreativeItem(Item::get(GunId::M1911));

        ItemFactory::registerItem(new ItemP08($this->getScheduler()), true);
        Item::addCreativeItem(Item::get(GunId::P08));

        ItemFactory::registerItem(new ItemM1Grand($this->getScheduler()), true);
        Item::addCreativeItem(Item::get(GunId::M1Garand));

        ItemFactory::registerItem(new ItemM1897($this->getScheduler()), true);
        Item::addCreativeItem(Item::get(GunId::M1897));
    }

    //GunSystem
    public function onTouchAir(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $this->gunSystemClient->tryShooting($item, $player,$this->getScheduler());
            }
        }
    }

    public function onTouch(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $this->gunSystemClient->tryShooting($item, $player,$this->getScheduler());

    }

    public function onBulletHit(ProjectileHitEntityEvent $event) {
        $entity = $event->getEntity();
        if($entity instanceof Egg) {
            $this->gunSystemClient->sendDamageByShooting($entity->getOwningEntity(), $event->getEntityHit());
        }
    }

    public function onSneak(PlayerToggleSneakEvent $event) {
        $player = $event->getPlayer();
        if ($player->isSneaking()) {
            $player->removeEffect(Effect::SLOWNESS);
        } else {
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS),null,5));
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