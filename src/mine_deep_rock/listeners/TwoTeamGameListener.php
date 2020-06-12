<?php


namespace mine_deep_rock\listeners;


use bossbarapi\BossBarAPI;
use mine_deep_rock\controllers\TwoTeamGameController;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\pmmp\entities\NPCBase;
use mine_deep_rock\pmmp\entities\TeamDeathMatchNPC;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_death_match_system\TeamDeathMatchSystem;
use team_system\TeamSystem;
use two_team_game_system\pmmp\events\AddScoreEvent;
use two_team_game_system\pmmp\events\GameFinishEvent;
use two_team_game_system\TwoTeamGameSystem;

class TwoTeamGameListener implements Listener
{
    /**
     * @var TwoTeamGameController
     */
    private $controller;
    private $server;
    private $scheduler;

    public function __construct(Server $server, TaskScheduler $scheduler) {
        $this->server = $server;
        $this->scheduler = $scheduler;
        $this->init(new TeamDeathMatchSystem($this->server, $this->scheduler, 30, 480, 1));
    }

    public function init(TwoTeamGameSystem $TwoTeamGameSystem): void {
        $this->controller = new TwoTeamGameController($TwoTeamGameSystem, $this->server, $this->scheduler);
    }

    public function onScoreAdded(AddScoreEvent $event): void {
        foreach (TeamSystem::getParticipantData($event->getGame()->getId()) as $playerData) {
            $bossBar = BossBarAPI::getInstance()->getBossBar($this->server->getPlayer($playerData->getName()));
            $bossBar->setTitle(TextFormat::BLUE . "Red:" . TextFormat::WHITE . $event->getRedTeamScore() . "---" . TextFormat::RED . "Blue:" . TextFormat::WHITE . $event->getBlueTeamScore());
        }
    }

    public function onTapNPC(EntityDamageByEntityEvent $event) {
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
        if ($attacker instanceof Player && $victim instanceof NPCBase) {
            switch ($victim::NAME) {
                //TODO:リネーム
                case TeamDeathMatchNPC::NAME;
                    $this->controller->join($attacker);
                    $event->setCancelled();
                    break;
                case CadaverEntity::NAME;
                    $event->setCancelled();
                    break;
            }
        }
    }

    public function onRegainHealth(EntityRegainHealthEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $this->controller->updateNameTag($player);
        }
    }

    public function onReceiveDamaged(EntityDamageByEntityEvent $event) {
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
        if ($attacker instanceof Player && $victim instanceof Player) {
            if ($this->controller->isJurisdiction($victim)) {
                if (!$this->controller->canReceiveDamage($attacker, $victim)) $event->setCancelled();
                $this->controller->updateNameTag($attacker);
            }
        }
    }

    public function onDead(PlayerDeathEvent $event): void {
        $victim = $event->getPlayer();
        $lastDamageCause = $victim->getLastDamageCause();
        if ($lastDamageCause instanceof EntityDamageByEntityEvent) {
            $attacker = $lastDamageCause->getDamager();
            if ($attacker instanceof Player) {
                if ($this->controller->isJurisdiction($victim)) {
                    $this->controller->onDead($attacker, $victim);
                }
            }
        }
    }

    public function onGameFinish(GameFinishEvent $event) {
        $this->controller->returnToLobby($event->getPlayers());
    }

    public function onTapWithItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            $item = $player->getInventory()->getItemInHand();
            $this->controller->useItem($player, $item);
        }
    }

    public function onTapByForTapUser(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $this->controller->useItem($player, $item);
            }
        }
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();
        $this->controller->displayDeathScreen($player);
    }

    public function existController(): bool {
        return $this->controller !== null;
    }
}