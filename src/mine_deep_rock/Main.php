<?php

namespace mine_deep_rock;

use mine_deep_rock\dao\DominationFlagDataDAO;
use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\GunRecord;
use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\pmmp\entity\GunDealerNPC;
use mine_deep_rock\pmmp\entity\GameMaster;
use mine_deep_rock\pmmp\event\UpdatedPlayerStatusEvent;
use mine_deep_rock\pmmp\form\CreateGameForm;
use mine_deep_rock\pmmp\form\DominationMapListForm;
use mine_deep_rock\pmmp\form\GunTypeListForSaleForm;
use mine_deep_rock\pmmp\form\ParticipantsListForm;
use mine_deep_rock\pmmp\form\ReceivedOneOnOneRequestsForm;
use mine_deep_rock\pmmp\form\SendOneOnOneRequestForm;
use mine_deep_rock\pmmp\form\StartGameForm;
use mine_deep_rock\pmmp\form\GameListForm;
use mine_deep_rock\pmmp\form\GameListToJoinForm;
use mine_deep_rock\pmmp\listener\BoxListener;
use mine_deep_rock\pmmp\listener\DominationListener;
use mine_deep_rock\pmmp\listener\GrenadeListener;
use mine_deep_rock\pmmp\listener\GunListener;
use mine_deep_rock\pmmp\listener\OneOnOneListener;
use mine_deep_rock\pmmp\listener\TDMListener;
use mine_deep_rock\pmmp\listener\TeamGameCommonListener;
use mine_deep_rock\pmmp\scoreboard\PlayerStatusScoreboard;
use mine_deep_rock\pmmp\service\SpawnGunDealerNPCPMMPService;
use mine_deep_rock\pmmp\service\SpawnGameMasterPMMPService;
use mine_deep_rock\pmmp\slot_menu\SettingEquipmentsMenu;
use mine_deep_rock\service\SendOneOnOneRequestService;
use mine_deep_rock\store\MilitaryDepartmentsStore;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use slot_menu_system\SlotMenuSystem;
use team_game_system\TeamGameSystem;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        Entity::registerEntity(GameMaster::class, true, ['GameMaster']);
        Entity::registerEntity(GunDealerNPC::class, true, ['GunDealerNPC']);
        Entity::registerEntity(CadaverEntity::class, true, ['Cadaver']);

        GunRecordDAO::init();
        PlayerStatusDAO::init();
        MilitaryDepartmentsStore::init();
        DominationFlagDataDAO::init();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->getServer()->getPluginManager()->registerEvents(new OneOnOneListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TDMListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new DominationListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TeamGameCommonListener($this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GunListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BoxListener($this->getServer(), $this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new GrenadeListener($this->getScheduler()), $this);
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $player->setGamemode(Player::ADVENTURE);

        $lobby = $this->getServer()->getLevelByName("lobby");
        $player->teleport($lobby->getSpawnLocation());

        SlotMenuSystem::send($player, new SettingEquipmentsMenu($this->getScheduler()));

        $playerName = $player->getName();

        if (!PlayerStatusDAO::isExist($playerName)) {
            PlayerStatusDAO::save(PlayerStatus::asNew($playerName));
        }

        if (!GunRecordDAO::isExist($playerName)) {
            GunRecordDAO::registerOwner($playerName);
            GunRecordDAO::add($playerName, GunRecord::asNew("M1907SL"));
            GunRecordDAO::add($playerName, GunRecord::asNew("MP18"));
            GunRecordDAO::add($playerName, GunRecord::asNew("Chauchat"));
            GunRecordDAO::add($playerName, GunRecord::asNew("SMLEMK3"));
            GunRecordDAO::add($playerName, GunRecord::asNew("Mle1903"));
        }

        if (!PlayerGameStatusStore::isExist($playerName)) {
            PlayerGameStatusStore::add(PlayerGameStatus::asNew($playerName));
        }

        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);
        PlayerStatusScoreboard::send($player);

        $player->sendMessage("ようこそMineDeepRockへ
BF1をリスペクトしたPVPサーバーです！
兵科と銃を選択して、ゲームに参加しましょう！");
    }

    public function onUpdatedPlayerStatus(UpdatedPlayerStatusEvent $event): void {
        $status = $event->getPlayerStatus();
        $player = $this->getServer()->getPlayer($status->getName());
        $playerData = TeamGameSystem::getPlayerData($player);
        if ($player->getLevel() !== null) {
            if ($playerData->getGameId() === null) {
                PlayerStatusScoreboard::update($player);
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if ($label === "spawnnpc") {
                if (count($args) !== 1) {
                    $sender->sendMessage("/spawnnpc [gm,gun]");
                    return false;
                }

                switch ($args[0]) {
                    case "gm":
                        SpawnGameMasterPMMPService::execute($sender->getLevel(), $sender->getPosition(), $sender->getYaw());
                        return true;
                        break;
                    case "gun":
                        SpawnGunDealerNPCPMMPService::execute($sender->getLevel(), $sender->getPosition(), $sender->getYaw());
                        return true;
                        break;
                }

                return false;

            } else if ($label === "creategame") {
                $sender->sendForm(new CreateGameForm());
                return true;

            } else if ($label === "startgame") {
                $sender->sendForm(new StartGameForm($this->getScheduler()));
                return true;

            } else if ($label === "view") {
                if (count($args) !== 1) {
                    $senderData = TeamGameSystem::getPlayerData($sender);
                    if ($senderData->getGameId() !== null) {
                        $game = TeamGameSystem::getGame($senderData->getGameId());
                        $sender->sendForm(new ParticipantsListForm($game));
                        return true;
                    } else {
                        return false;
                    }
                }

                switch ($args[0]) {
                    case "tdm":
                        $sender->sendForm(new GameListForm(GameTypeList::TDM()));
                        return true;
                        break;
                    case "domination":
                        $sender->sendForm(new GameListForm(GameTypeList::Domination()));
                        return true;
                        break;
                }
                return false;
            } else if ($label === "flag") {
                $sender->sendForm(new DominationMapListForm());
                return true;
            } else if ($label === "vs") {
                if (count($args) !== 1) return false;
                $receiverName = $args[0];
                if ($this->getServer()->getPlayer($receiverName) === null) {
                    $sender->sendMessage("{$receiverName}は存在しません");
                    return true;
                }
                if (!$this->getServer()->getPlayer($receiverName)->isOnline()) {
                    $sender->sendMessage("{$receiverName}はオフラインです");
                    return true;
                }
                $sender->sendForm(new SendOneOnOneRequestForm($receiverName));
                return true;
            } else if ($label === "requests") {
                $sender->sendForm(new ReceivedOneOnOneRequestsForm($sender));
            }
        }
        return false;
    }

    public function onTapNPC(EntityDamageByEntityEvent $event): void {
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
        if ($attacker instanceof Player) {
            if ($victim instanceof GameMaster) {
                if ($attacker->getInventory()->getItemInHand()->getId() === ItemIds::WOODEN_SWORD && $attacker->isOp()) {
                    $victim->kill();
                } else {
                    $attacker->sendForm(new GameListToJoinForm());
                    $event->setCancelled();
                }
            } else if ($victim instanceof GunDealerNPC) {
                if ($attacker->getInventory()->getItemInHand()->getId() === ItemIds::WOODEN_SWORD  && $attacker->isOp()) {
                    $victim->kill();
                } else {
                    $attacker->sendForm(new GunTypeListForSaleForm($this->getScheduler()));
                    $event->setCancelled();
                }
            }
        }
    }

    public function onDamaged(EntityDamageEvent $event): void {
        $victim = $event->getEntity();
        if ($victim instanceof Player) {
            if ($victim->getLevel()->getName() === "lobby") {
                $event->setCancelled();
            }
        }
    }

    public function onExhaust(PlayerExhaustEvent $event) {
        $event->setCancelled();
    }
}