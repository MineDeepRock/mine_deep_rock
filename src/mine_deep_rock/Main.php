<?php

namespace mine_deep_rock;

use bossbarapi\BossBarAPI;
use mine_deep_rock\pmmp\entities\NPCBase;
use mine_deep_rock\pmmp\entities\TeamDeathMatchNPC;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
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
        $this->teamDeathMatchSystem = new TeamDeathMatchSystem($this->getServer(), $this->getScheduler(), 30, 480,4);
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
            switch ($victim->getName()) {
                case TeamDeathMatchNPC::NAME;
                $this->teamDeathMatchSystem->join($attacker);
                break;
            }
        }
    }

    public function onGameFinish(GameFinishEvent $event){
        //Lobbyに戻す
        foreach ($event->getPlayers() as $player) {
            $level = $this->getServer()->getLevelByName("lobby");
            $pos = $level->getSpawnLocation();
            $player->teleport($pos);
        }
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
                        $this->teamDeathMatchSystem->setSpawnPoint($attacker);
                        break;
                }
            }
        }
    }
}