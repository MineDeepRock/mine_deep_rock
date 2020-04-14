<?php

use gun_system\GunSystemClient;
use gun_system\models\GunId;
use gun_system\pmmp\items\ItemHandGun;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
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

        ItemFactory::registerItem(new ItemHandGun($this->getScheduler()), true);
        Item::addCreativeItem(Item::get(GunId::HAND_GUN));
    }

    //GunSystem
    public function onTouchAir(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $this->gunSystemClient->tryShooting($item, $player);
            }
        }
    }

    public function onTouch(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $this->gunSystemClient->tryShooting($item, $player);

    }

    public function onDamage(EntityDamageEvent $event) {
        if ($event instanceof EntityDamageByEntityEvent) {
            $this->gunSystemClient->damageByShooting($event->getDamager(), $event);
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