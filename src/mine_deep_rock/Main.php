<?php

namespace mine_deep_rock;

use bossbarapi\BossBarAPI;
use gun_system\models\assault_rifle\M1907SL;
use gun_system\models\hand_gun\Mle1903;
use gun_system\models\light_machine_gun\Chauchat;
use gun_system\models\sniper_rifle\SMLEMK3;
use gun_system\models\sub_machine_gun\MP18;
use mine_deep_rock\pmmp\commands\NPCCommand;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\pmmp\entities\NPCBase;
use mine_deep_rock\pmmp\entities\TeamDeathMatchNPC;
use mine_deep_rock\pmmp\items\RespawnItem;
use mine_deep_rock\slot_menus\EquipmentSelectMenu;
use money_system\MoneySystem;
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
use slot_menu_system\SlotMenuSystem;
use team_death_match_system\TeamDeathMatchSystem;
use team_system\TeamSystem;
use two_team_game_system\pmmp\events\AddScoreEvent;
use two_team_game_system\pmmp\events\GameFinishEvent;
use weapon_data_system\WeaponDataSystem;

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

        if (!WeaponDataSystem::isExist($player->getName())) {
            WeaponDataSystem::init($player->getName());
            WeaponDataSystem::add($player->getName(), M1907SL::NAME);
            WeaponDataSystem::add($player->getName(), MP18::NAME);
            WeaponDataSystem::add($player->getName(), Chauchat::NAME);
            WeaponDataSystem::add($player->getName(), SMLEMK3::NAME);
            WeaponDataSystem::add($player->getName(), Mle1903::NAME);
            MoneySystem::increase($player->getName(), 5000);
        }

        SlotMenuSystem::send($player, new EquipmentSelectMenu());
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
            $this->useItem($player, $item);
        }
    }

    public function onTapByForTapUser(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        if ($packet instanceof LevelSoundEventPacket) {
            if ($packet->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $player = $event->getPlayer();
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $this->useItem($player, $item);
            }
        }
    }

    public function useItem(Player $player, Item $item): void {
        if ($item instanceof RespawnItem) {
            $this->spawn($player);
            return;
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