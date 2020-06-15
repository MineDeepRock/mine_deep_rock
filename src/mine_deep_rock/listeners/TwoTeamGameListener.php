<?php


namespace mine_deep_rock\listeners;


use bossbarapi\BossBarAPI;
use mine_deep_rock\interpreters\TwoTeamGameInterpreter;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\pmmp\entities\TeamDeathMatchNPC;
use mine_deep_rock\pmmp\items\RespawnItem;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
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
use two_team_game_system\pmmp\events\GameStartEvent;
use two_team_game_system\TwoTeamGameSystem;

class TwoTeamGameListener implements Listener
{
    /**
     * @var TwoTeamGameInterpreter
     */
    private $interpreter;
    private $server;
    private $scheduler;

    public function __construct(Server $server, TaskScheduler $scheduler) {
        $this->server = $server;
        $this->scheduler = $scheduler;
        $this->init(new TeamDeathMatchSystem($this->server, $this->scheduler, 30, 480, 1));
    }

    public function init(TwoTeamGameSystem $TwoTeamGameSystem): void {
        $this->interpreter = new TwoTeamGameInterpreter($TwoTeamGameSystem, $this->server, $this->scheduler);
    }

    public function onScoreAdded(AddScoreEvent $event): void {
        foreach (TeamSystem::getParticipantData($event->getGame()->getId()) as $playerData) {
            $bossBar = BossBarAPI::getInstance()->getBossBar($this->server->getPlayer($playerData->getName()));
            $bossBar->setTitle(TextFormat::BLUE . "Red:" . TextFormat::WHITE . $event->getRedTeamScore() . "---" . TextFormat::RED . "Blue:" . TextFormat::WHITE . $event->getBlueTeamScore());
        }
    }

    public function onGameStart(GameStartEvent $event) {
        foreach ($event->getPlayers() as $player) {
            if ($player->isOnline()) {
                $this->interpreter->spawn($player);
                $player->setNameTagVisible(false);
            }
        }
    }

    public function onJoinServer(PlayerJoinEvent $event) {
        $this->interpreter->onJoinServer($event->getPlayer());
    }

    public function onTapNPC(EntityDamageByEntityEvent $event) {
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
        if ($attacker instanceof Player) {
            if ($victim instanceof TeamDeathMatchNPC) {
                $this->interpreter->joinGame($attacker);
                $event->setCancelled();
            } else if ($victim instanceof CadaverEntity) {
                $this->interpreter->resuscitate($attacker, $victim);
                $event->setCancelled();
            }
        }
    }

    public function onRegainHealth(EntityRegainHealthEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $this->interpreter->onRegainHealth($player);
        }
    }

    public function onReceiveDamaged(EntityDamageByEntityEvent $event) {
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
        if ($attacker instanceof Player && $victim instanceof Player) {
            if ($this->interpreter->onReceiveDamage($attacker, $victim, $event->getFinalDamage())) {
                $event->setAttackCooldown(0);
                $event->setKnockBack(0);
            } else {
                $event->setCancelled();
            }
        }
    }

    public function onDead(PlayerDeathEvent $event): void {
        $victim = $event->getPlayer();
        $lastDamageCause = $victim->getLastDamageCause();
        if ($lastDamageCause instanceof EntityDamageByEntityEvent) {
            $attacker = $lastDamageCause->getDamager();
            if ($attacker instanceof Player) {
                if ($this->interpreter->isJurisdiction($victim)) {
                    $this->interpreter->onDead($attacker, $victim);
                }
            }
        }
    }

    public function onGameFinish(GameFinishEvent $event) {
        $this->interpreter->onGameFinish($event->getPlayers());
    }

    public function onTapWithItem(PlayerInteractEvent $event) {
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $player = $event->getPlayer();
            $item = $player->getInventory()->getItemInHand();
            if ($item instanceof RespawnItem) {
                $this->interpreter->useRespawnItem($player);
                return;
            }
        }
    }

    public function onTapByForTapUser(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                if ($item instanceof RespawnItem) {
                    $this->interpreter->useRespawnItem($player);
                    return;
                }
            }
        }
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();
        $this->interpreter->onPlayerRespawn($player);
    }
}