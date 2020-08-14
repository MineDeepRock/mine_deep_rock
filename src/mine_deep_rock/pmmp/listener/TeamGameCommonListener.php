<?php


namespace mine_deep_rock\pmmp\listener;


use bossbar_system\BossBar;
use box_system\pmmp\entities\BoxEntity;
use grenade_system\pmmp\entities\GrenadeEntity;
use gun_system\pmmp\item\ItemGun;
use mine_deep_rock\pmmp\BossBarTypes;
use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\pmmp\service\RescuePlayerPMMPService;
use mine_deep_rock\pmmp\service\SendGameScoreToParticipantsPMMPService;
use mine_deep_rock\pmmp\service\SendKillLogPMMPService;
use mine_deep_rock\pmmp\service\SendKillMessagePMMPService;
use mine_deep_rock\pmmp\service\SendParticipantsToLobbyPMMPService;
use mine_deep_rock\pmmp\service\SpawnCadaverEntityPMMPService;
use mine_deep_rock\pmmp\service\UpdatePrivateNameTagPMMPService;
use mine_deep_rock\service\AddKillCountInGameService;
use mine_deep_rock\service\AddKillCountToGunRecordService;
use mine_deep_rock\service\GivePlayerMoneyService;
use mine_deep_rock\service\ResetPlayerGameStatusService;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use private_name_tag\models\PrivateNameTag;
use team_game_system\pmmp\event\FinishedGameEvent;
use team_game_system\pmmp\event\PlayerKilledPlayerEvent;
use team_game_system\TeamGameSystem;

class TeamGameCommonListener implements Listener
{

    /**
     * @var TaskScheduler
     */
    private $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
    }

    public function onFinishedGame(FinishedGameEvent $event): void {
        $game = $event->getGame();
        $playersData = $event->getPlayersData();
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

        SendGameScoreToParticipantsPMMPService::execute($playersData);

        //勝敗のメッセージ
        //プレイヤーのアイテム削除、ゲームモード修正
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

            //PlayerGameStatusをリセット
            ResetPlayerGameStatusService::execute($player->getName());

            $player->getInventory()->setContents([]);
            $player->getArmorInventory()->setContents([]);
            $player->setGamemode(Player::ADVENTURE);
            $player->setImmobile(false);
        }

        //15秒後にロビーに送る
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($playersData, $game): void {
            SendParticipantsToLobbyPMMPService::execute($playersData, $this->scheduler);
        }), 20 * 15);
    }

    public function onTapCadaverEntity(EntityDamageByEntityEvent $event) {
        $player = $event->getDamager();
        $cadaverEntity = $event->getEntity();
        if ($player instanceof Player) {
            if ($cadaverEntity instanceof CadaverEntity) {
                if ($cadaverEntity->getOwner() === null) return;
                $owner = $cadaverEntity->getOwner();
                if (!$owner->isOnline()) return;
                if ($owner->getName() === $player->getName()) return;
                $playerData = TeamGameSystem::getPlayerData($player);
                $ownerData = TeamGameSystem::getPlayerData($owner);
                if ($playerData->getGameId() === null || $ownerData->getGameId() === null) return;

                RescuePlayerPMMPService::execute($player, $cadaverEntity->getOwner());
                $event->setCancelled();
            }
        }
    }

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

    public function onDead(PlayerDeathEvent $event) {
        $event->setDrops([]);

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

    public function onPlayerKilledPlayer(PlayerKilledPlayerEvent $event): void {
        $attacker = $event->getAttacker();

        //アタッカーにお金を付与
        GivePlayerMoneyService::execute($attacker->getName(), 100);
        AddKillCountInGameService::execute($attacker->getName());
        $item = $attacker->getInventory()->getItemInHand();
        if ($item instanceof ItemGun) {
            AddKillCountToGunRecordService::execute($attacker->getName(), $item->getCustomName());
        }

        //その場をスポーン地点に
        $victim = $event->getTarget();
        $victim->setSpawn($victim->getPosition());

        //死体を出す
        SpawnCadaverEntityPMMPService::execute($victim);
    }
}