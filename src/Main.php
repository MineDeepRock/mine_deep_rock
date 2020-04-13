<?php

use gun_system\pmmp\items\ItemHandGun;
use pocketmine\entity\projectile\Egg;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use team_system\pmmp\command\TeamCommand;
use team_system\services\MemberService;
use team_system\services\TeamService;
use team_system\TeamSystemClient;
use team_system\TeamSystemNotifier;

class Main extends PluginBase implements Listener
{
    private $teamSystemClient;

    function onEnable() {
        $this->teamSystemClient = new TeamSystemClient(new TeamService(), new MemberService(),new TeamSystemNotifier(function (){
            //TODO:
        }));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("team", new TeamCommand($this, $this->teamSystemClient));

        ItemFactory::registerItem(new ItemHandGun(Item::STICK, $this->getScheduler()), true);
        Item::addCreativeItem(Item::get(Item::STICK));
    }

    public function onTouch(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $itemName = $event->getItem()->getName();
        switch ($itemName) {
            case "HandGun":
                $item->shoot($player);
        }
    }

    public function onDamage(EntityDamageEvent $event) {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager->getInventory()->getItemInHand()->getId() == Item::STICK) {
                //武器ごとに条件分岐
                //TODO
                $event->setBaseDamage(5);
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->teamSystemClient->onJoin($playerName);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();

        $this->teamSystemClient->onLeave($playerName);

    }
}