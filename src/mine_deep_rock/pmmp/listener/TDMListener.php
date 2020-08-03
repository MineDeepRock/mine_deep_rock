<?php

namespace mine_deep_rock\pmmp\listener;


use box_system\pmmp\entities\BoxEntity;
use grenade_system\pmmp\entities\GrenadeEntity;
use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\pmmp\service\GetPlayerReadyToTDMPMMPService;
use mine_deep_rock\pmmp\service\GetPlayersReadyToTDMPMMPService;
use mine_deep_rock\pmmp\service\RescuePlayerPMMPService;
use mine_deep_rock\pmmp\service\SendParticipantsToLobbyPMMPService;
use mine_deep_rock\pmmp\service\SpawnCadaverEntityPMMPService;
use mine_deep_rock\pmmp\service\UpdateTDMBossBarPMMPService;
use mine_deep_rock\pmmp\service\UpdateTDMScoreboardPMMPService;
use mine_deep_rock\pmmp\slot_menu\SettingEquipmentsOnTDMMenu;
use mine_deep_rock\service\GivePlayerMoneyService;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use slot_menu_system\SlotMenuSystem;
use team_game_system\model\Score;
use team_game_system\pmmp\event\AddedScoreEvent;
use team_game_system\pmmp\event\FinishedGameEvent;
use team_game_system\pmmp\event\PlayerJoinedGameEvent;
use team_game_system\pmmp\event\PlayerKilledPlayerEvent;
use team_game_system\pmmp\event\StartedGameEvent;
use team_game_system\pmmp\event\UpdatedGameTimerEvent;
use team_game_system\TeamGameSystem;

class TDMListener implements Listener
{
    /**
     * @var TaskScheduler
     */
    private $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
    }

    public function onJoinGame(PlayerJoinedGameEvent $event) {
        $gameId = $event->getGameId();
        if (in_array($gameId, TDMGameIdsStore::getAll())) {
            $game = TeamGameSystem::getGame($gameId);
            $player = $event->getPlayer();
            if ($game->isStarted()) {
                GetPlayerReadyToTDMPMMPService::execute(TeamGameSystem::getPlayerData($player), $gameId);
            } else {
                //10人でスタート
                $playersCount = TeamGameSystem::getGamePlayersData($gameId);
                if ($playersCount === 10) {
                    TeamGameSystem::startGame($this->scheduler, $gameId);
                }
            }
        }
    }

    public function onPlayerKilledPlayer(PlayerKilledPlayerEvent $event): void {
        $attacker = $event->getAttacker();
        $attackerData = TeamGameSystem::getPlayerData($attacker);

        if (in_array($attackerData->getGameId(), TDMGameIdsStore::getAll())) {
            //アタッカーのチームにスコアを追加
            TeamGameSystem::addScore($attackerData->getGameId(), $attackerData->getTeamId(), new Score(1));
            GivePlayerMoneyService::execute($attacker->getName(), 100);

            //その場をスポーン地点に
            $victim = $event->getTarget();
            $victim->setSpawn($victim->getPosition());

            //死体を出す
            SpawnCadaverEntityPMMPService::execute($victim);
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event) {
        $event->setDrops([]);
    }

    public function onUpdatedTime(UpdatedGameTimerEvent $event): void {
        $gameId = $event->getGameId();


        if (in_array($gameId, TDMGameIdsStore::getAll())) {
            $playersData = TeamGameSystem::getGamePlayersData($gameId);
            $timeLimit = $event->getTimeLimit();
            $elapsedTime = $event->getElapsedTime();
            UpdateTDMBossBarPMMPService::execute($playersData, $timeLimit, $elapsedTime);
        }
    }

    public function onAddedScore(AddedScoreEvent $event): void {
        $gameId = $event->getGameId();
        if (in_array($gameId, TDMGameIdsStore::getAll())) {
            UpdateTDMScoreboardPMMPService::execute($gameId);
        }
    }

    public function onStartedGame(StartedGameEvent $event) {
        $gameId = $event->getGameId();
        if (in_array($gameId, TDMGameIdsStore::getAll())) {
            GetPlayersReadyToTDMPMMPService::execute($gameId);
        }
    }

    //TODO :Not Only TDM
    public function onFinishedGame(FinishedGameEvent $event): void {
        $game  = $event->getGame();
        SendParticipantsToLobbyPMMPService::execute($event->getPlayersData(), $this->scheduler);
        TDMGameIdsStore::delete($game->getId());

        //TODO:終わった試合の参加者のエンティティだったらKillにする
        $level = Server::getInstance()->getLevelByName($game->getMap()->getLevelName());
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof BoxEntity) {
                $entity->kill();
            } else if ($entity instanceof GrenadeEntity) {
                $entity->kill();
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        $playerData = TeamGameSystem::getPlayerData($player);
        if (in_array($playerData->getGameId(), TDMGameIdsStore::getAll())) {
            $player->setGamemode(Player::SPECTATOR);
            $player->setImmobile(true);

            $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player): void {
                if ($player->isOnline()) SlotMenuSystem::send($player, new SettingEquipmentsOnTDMMenu($this->scheduler));
            }), 20 * 5);
        }
    }

    //TODO :Not Only TDM
    public function onTapCadaverEntity(EntityDamageByEntityEvent $event) {
        $player = $event->getDamager();
        $cadaverEntity = $event->getEntity();
        if ($player instanceof Player) {
            if ($cadaverEntity instanceof CadaverEntity) {
                RescuePlayerPMMPService::execute($player, $cadaverEntity->getOwner());
                $event->setCancelled();
            }
        }
    }
}