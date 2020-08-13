<?php

namespace mine_deep_rock\pmmp\listener;


use bossbar_system\BossBar;
use bossbar_system\model\BossBarType;
use box_system\pmmp\entities\BoxEntity;
use grenade_system\pmmp\entities\GrenadeEntity;
use mine_deep_rock\pmmp\BossBarTypes;
use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\pmmp\service\GetPlayerReadyToTDMPMMPService;
use mine_deep_rock\pmmp\service\GetPlayersReadyToTDMPMMPService;
use mine_deep_rock\pmmp\service\RescuePlayerPMMPService;
use mine_deep_rock\pmmp\service\ResortToTDMPMMPService;
use mine_deep_rock\pmmp\service\SendKillLogPMMPService;
use mine_deep_rock\pmmp\service\SendKillMessagePMMPService;
use mine_deep_rock\pmmp\service\SendParticipantsToLobbyPMMPService;
use mine_deep_rock\pmmp\service\SpawnCadaverEntityPMMPService;
use mine_deep_rock\pmmp\service\UpdatePrivateNameTagPMMPService;
use mine_deep_rock\pmmp\service\SendTDMBossBarPMMPService;
use mine_deep_rock\pmmp\service\UpdateTDMScoreboardPMMPService;
use mine_deep_rock\pmmp\slot_menu\SettingEquipmentsOnTDMMenu;
use mine_deep_rock\service\GivePlayerMoneyService;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use private_name_tag\models\PrivateNameTag;
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
            //アタッカーにお金を付与
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
            SendTDMBossBarPMMPService::execute($playersData, $timeLimit, $elapsedTime);
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
        $game = $event->getGame();
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

        //勝敗のメッセージ
        //プレイヤーのアイテム削除、ゲームモード修正
        $playersData = $event->getPlayersData();
        $wonTeam = $event->getWonTeam();
        foreach ($playersData as $playerData) {
            $player = Server::getInstance()->getPlayer($playerData->getName());
            if ($wonTeam === null) {
                $player->sendMessage("引き分け");
                $player->sendTitle("引き分け");
            } else if ($wonTeam->getId()->equals($playerData->getTeamId())) {
                $player->sendMessage("勝ち！");
                $player->sendTitle("勝ち！");
            } else {
                $player->sendMessage("負け...");
                $player->sendTitle("負け...");
            }

            //levelにいるエンティティ全取得する実装なのでココで消さないとまずい
            $tag = PrivateNameTag::get($player);
            if ($tag !== null) $tag->remove();

            $bossBar = BossBar::findByType($player, BossBarTypes::TDM());
            if ($bossBar !== null) $bossBar->remove();

            $player->getInventory()->setContents([]);
            $player->setGamemode(Player::ADVENTURE);
            $player->setImmobile(false);
        }

        //15秒後にロビーに送る
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($playersData, $game): void {
            SendParticipantsToLobbyPMMPService::execute($playersData, $this->scheduler);
        }), 20 * 15);
    }

    public function onRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        $playerData = TeamGameSystem::getPlayerData($player);
        if (in_array($playerData->getGameId(), TDMGameIdsStore::getAll())) {
            $game = TeamGameSystem::getGame($playerData->getGameId());
            if ($game->isClosed()) return;

            $player->setGamemode(Player::SPECTATOR);
            $player->setImmobile(true);

            $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player): void {
                if ($player->isOnline()){
                    if ($player->getGamemode() === Player::SPECTATOR) {
                        SlotMenuSystem::send($player, new SettingEquipmentsOnTDMMenu($this->scheduler));
                    }
                }
            }), 20 * 5);

            $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player): void {
                if ($player->isOnline()){
                    if ($player->getGamemode() === Player::SPECTATOR) {
                        ResortToTDMPMMPService::execute($player);
                    }
                }
            }), 20 * 30);
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

    //TODO :Not Only TDM
    public function onRegainHealth(EntityRegainHealthEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $playerData = TeamGameSystem::getPlayerData($player);
            if ($playerData->getGameId() !== null) {
                $game = TeamGameSystem::getGame($playerData->getGameId());
                if ($game->isStarted()) {
                    UpdatePrivateNameTagPMMPService::execute($player);
                }
            }
        }
    }

    //TODO :Not Only TDM
    public function onDamaged(EntityDamageEvent $event) {
        if ($event->isCancelled()) return;

        $player = $event->getEntity();
        if ($player instanceof Player) {
            $playerData = TeamGameSystem::getPlayerData($player);
            if ($playerData->getGameId() !== null) {
                $game = TeamGameSystem::getGame($playerData->getGameId());
                if ($game->isClosed()) return;
                if ($game->isStarted()) {
                    UpdatePrivateNameTagPMMPService::execute($player, $player->getHealth() - $event->getFinalDamage());
                }
            }
        }
    }

    //TODO :Not Only TDM
    public function onDead(PlayerDeathEvent $event) {
        $victim = $event->getPlayer();
        $victimData = TeamGameSystem::getPlayerData($victim);
        if ($victimData->getGameId() === null) return;

        $cause = $victim->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $attacker = $cause->getDamager();
            if ($attacker instanceof Player) {
                $event->setDeathMessage("");
                SendKillLogPMMPService::execute($attacker, $victim);
                SendKillMessagePMMPService::execute($attacker, $victim);
            }
        }
    }
}