<?php

namespace mine_deep_rock;

use bossbarapi\BossBarAPI;
use mine_deep_rock\pmmp\commands\NPCCommand;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\pmmp\entities\NPCBase;
use mine_deep_rock\pmmp\entities\TeamDeathMatchNPC;
use mine_deep_rock\pmmp\items\RespawnItem;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use team_death_match_system\TeamDeathMatchSystem;
use team_system\TeamSystem;
use two_team_game_system\pmmp\events\AddScoreEvent;
use two_team_game_system\pmmp\events\GameFinishEvent;

class Main extends PluginBase implements Listener
{
    /**
     * @var TeamDeathMatchSystem
     */
    private $teamDeathMatchSystem;

    public function onEnable() {
        Entity::registerEntity(TeamDeathMatchNPC::class, true, ['TeamDeathMatch']);

        $this->teamDeathMatchSystem = new TeamDeathMatchSystem($this->getServer(), $this->getScheduler(), 30, 480, 1);
        $this->getServer()->getCommandMap()->register("npc", new NPCCommand($this));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onScoreAdded(AddScoreEvent $event): void {
        foreach (TeamSystem::getParticipantData($event->getGame()->getId()) as $playerData) {
            $bossBar = BossBarAPI::getInstance()->getBossBar($this->getServer()->getPlayer($playerData->getName()));
            $bossBar->setTitle(TextFormat::BLUE . "Red:" . TextFormat::WHITE . $event->getRedTeamScore() . "---" . TextFormat::RED . "Blue:" . TextFormat::WHITE . $event->getBlueTeamScore());
        }
    }

    //General
    public function onTapNPC(EntityDamageByEntityEvent $event) {
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
        if ($attacker instanceof Player && $victim instanceof NPCBase) {
            switch ($victim::NAME) {
                case TeamDeathMatchNPC::NAME;
                    $this->teamDeathMatchSystem->join($attacker);
                    $event->setCancelled();
                    break;
                case CadaverEntity::NAME;
                    $event->setCancelled();
                    break;
            }
        }
    }

    public function onGameFinish(GameFinishEvent $event) {
        //Lobbyに戻す
        foreach ($event->getPlayers() as $player) {
            $level = $this->getServer()->getLevelByName("lobby");
            $pos = $level->getSpawnLocation();
            $player->teleport($pos);
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);
    }

    //TeamDeathMatch
    public function onReceiveDamaged(EntityDamageByEntityEvent $event) {
        $victim = $event->getEntity();
        $attacker = $event->getDamager();
        if ($attacker instanceof Player && $victim instanceof Player) {
            switch ($attacker->getLevel()->getName()) {
                case $this->teamDeathMatchSystem->getMap()->getName():
                    if (!$this->teamDeathMatchSystem->canReceiveDamage($attacker, $victim)) $event->setCancelled();
                    break;
            }
        }
    }

    public function onDead(PlayerDeathEvent $event): void {
        $victim = $event->getPlayer();
        $lastDamageCause = $victim->getLastDamageCause();
        if ($lastDamageCause instanceof EntityDamageByEntityEvent) {
            $attacker = $lastDamageCause->getDamager();
            if ($attacker instanceof Player) {
                switch ($attacker->getLevel()->getName()) {
                    case $this->teamDeathMatchSystem->getMap()->getName():
                        $this->teamDeathMatchSystem->addScore($attacker);
                        $this->sendKillMessage($attacker, $victim);
                        $victim->setSpawn($victim->getPosition());

                        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim);
                        $cadaverEntity->spawnToAll();
                        break;
                }
            }
        }
    }

    public function onTapWithItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            $item = $player->getInventory()->getItemInHand();
            $this->useItem($player,$item);
        }
    }

    public function onTapByForTapUser(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $this->useItem($player,$item);
            }
        }
    }

    public function useItem(Player $player, Item $item) {
        switch ($item->getId()) {
            case RespawnItem::ITEM_ID:
                $this->spawn($player);
                break;
        }
    }

    public function spawn(Player $player): void {
        $player->setGamemode(Player::ADVENTURE);
        $player->setImmobile(false);
        $player->teleport($player->getSpawn());
        $this->setInitInventory($player);
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $player->getName()) {
                    $entity->kill();
                }
            }
        }
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();
        $this->displayDeathScreen($player);
    }

    public function setInitInventory(Player $player): void {
        $player->getInventory()->setContents([]);
    }

    public function displayDeathScreen(Player $player): void {
        $cadaverEntity = null;

        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $player->getName()) {
                    $cadaverEntity = $entity;
                }
            }
        }

        $player->getInventory()->setContents([]);
        $player->setGamemode(Player::SPECTATOR);
        $player->setImmobile(true);
        $this->teamDeathMatchSystem->setSpawnPoint($player);
        if ($cadaverEntity !== null) $player->teleport($cadaverEntity->getPosition()->add(0, 1, 0));
        $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player): void {
            $player->getInventory()->addItem(new RespawnItem());
        }), 20 * 5);
    }

    public function sendKillMessage(Player $attacker, Player $victim): void {
        $attackerWeapon = $attacker->getInventory()->getItemInHand();
        $message = $attacker->getNameTag() . " " . $attackerWeapon->getCustomName() . " " . $victim->getNameTag();
        foreach ($attacker->getLevel()->getPlayers() as $player) {
            $player->sendMessage($message);
        }
    }
}