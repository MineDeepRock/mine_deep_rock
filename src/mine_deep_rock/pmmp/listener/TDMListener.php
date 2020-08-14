<?php

namespace mine_deep_rock\pmmp\listener;


use gun_system\pmmp\item\ItemGun;
use mine_deep_rock\pmmp\service\GetPlayerReadyToTDMPMMPService;
use mine_deep_rock\pmmp\service\GetPlayersReadyToTDMPMMPService;
use mine_deep_rock\pmmp\service\ResortToTDMPMMPService;
use mine_deep_rock\pmmp\service\ShowPrivateNameTagToAllyPMMPService;
use mine_deep_rock\pmmp\service\SpawnCadaverEntityPMMPService;
use mine_deep_rock\pmmp\service\SendTDMBossBarPMMPService;
use mine_deep_rock\pmmp\service\UpdateTDMScoreboardPMMPService;
use mine_deep_rock\pmmp\slot_menu\SettingEquipmentsOnGameMenu;
use mine_deep_rock\service\AddKillCountInGameService;
use mine_deep_rock\service\AddKillCountToGunRecordService;
use mine_deep_rock\service\GivePlayerMoneyService;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use slot_menu_system\SlotMenuSystem;
use team_game_system\pmmp\event\AddedScoreEvent;
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
                //ネームタグをセット
                $player->setNameTagAlwaysVisible(false);
                $playerData = TeamGameSystem::getPlayerData($player);
                ShowPrivateNameTagToAllyPMMPService::execute($player, $playerData->getTeamId());
                GetPlayerReadyToTDMPMMPService::execute($playerData, $gameId);
            }
            //else {
            //    //10人でスタート
            //    $playersCount = TeamGameSystem::getGamePlayersData($gameId);
            //    if ($playersCount === 10) {
            //        TeamGameSystem::startGame($this->scheduler, $gameId);
            //    }
            //}
        }
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

    public function onRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        $playerData = TeamGameSystem::getPlayerData($player);
        if (in_array($playerData->getGameId(), TDMGameIdsStore::getAll())) {
            $game = TeamGameSystem::getGame($playerData->getGameId());
            if ($game->isClosed()) return;

            $player->setGamemode(Player::SPECTATOR);
            $player->setImmobile(true);

            $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player, $game): void {
                if ($game->isClosed()) {
                    $player->setGamemode(Player::ADVENTURE);
                    $player->setImmobile(false);
                }

                if ($player->isOnline()) {
                    if ($player->getGamemode() === Player::SPECTATOR) {
                        SlotMenuSystem::send($player, new SettingEquipmentsOnGameMenu($this->scheduler));
                    }
                }
            }), 20 * 5);

            $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player, $game): void {
                if ($game->isClosed()) return;

                if ($player->isOnline()) {
                    if ($player->getGamemode() === Player::SPECTATOR) {
                        ResortToTDMPMMPService::execute($player);
                    }
                }
            }), 20 * 30);
        }
    }
}