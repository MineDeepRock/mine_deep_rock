<?php

namespace mine_deep_rock;

use mine_deep_rock\dao\GunRecordDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\GunRecord;
use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\model\PlayerStatus;
use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\pmmp\entity\TeamDeathMatchNPC;
use mine_deep_rock\pmmp\event\UpdatedPlayerStatusEvent;
use mine_deep_rock\pmmp\form\CreateGameForm;
use mine_deep_rock\pmmp\form\ParticipantsListForm;
use mine_deep_rock\pmmp\form\StartGameForm;
use mine_deep_rock\pmmp\form\TDMListForm;
use mine_deep_rock\pmmp\form\TDMListToJoinForm;
use mine_deep_rock\pmmp\listener\BoxListener;
use mine_deep_rock\pmmp\listener\GrenadeListener;
use mine_deep_rock\pmmp\listener\GunListener;
use mine_deep_rock\pmmp\listener\TDMListener;
use mine_deep_rock\pmmp\scoreboard\PlayerStatusScoreboard;
use mine_deep_rock\pmmp\service\SpawnTeamDeathMatchNPCPMMPService;
use mine_deep_rock\pmmp\slot_menu\SettingEquipmentsMenu;
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
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use slot_menu_system\SlotMenuSystem;
use team_game_system\TeamGameSystem;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        Entity::registerEntity(TeamDeathMatchNPC::class, true, ['TeamDeathMatchNPC']);
        Entity::registerEntity(CadaverEntity::class, true, ['Cadaver']);

        GunRecordDAO::init();
        PlayerStatusDAO::init();
        MilitaryDepartmentsStore::init();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new TDMListener($this->getScheduler()), $this);
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
                    $sender->sendMessage("/spawnnpc [npc]");
                    return false;
                }

                switch ($args[0]) {
                    case "tdm":
                        SpawnTeamDeathMatchNPCPMMPService::execute($sender->getLevel(), $sender->getPosition(), $sender->getYaw());
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
                        $sender->sendForm(new TDMListForm());
                        return true;
                        break;
                }
                return false;
            }
        }
        return false;
    }

    public function onTapNPC(EntityDamageByEntityEvent $event): void {
        $attacker = $event->getDamager();
        $victim = $event->getEntity();
        if ($attacker instanceof Player && $victim instanceof TeamDeathMatchNPC) {
            $attacker->sendForm(new TDMListToJoinForm());
            $event->setCancelled();
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