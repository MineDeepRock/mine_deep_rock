<?php


namespace mine_deep_rock\controllers;


use gun_system\GunSystem;
use military_department_system\MilitaryDepartmentSystem;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\pmmp\items\RespawnItem;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_death_match_system\TeamDeathMatchSystem;
use team_system\TeamSystem;
use two_team_game_system\TwoTeamGameSystem;
use weapon_data_system\models\GunData;
use weapon_data_system\WeaponDataSystem;

class TwoTeamGameController
{

    private $twoTeamGameSystem;
    private $server;
    private $scheduler;

    public function __construct(TwoTeamGameSystem $twoTeamGameSystem, Server $server, TaskScheduler $scheduler) {
        $this->twoTeamGameSystem = $twoTeamGameSystem;
        $this->server = $server;
        $this->scheduler = $scheduler;
    }

    public function init(TwoTeamGameSystem $twoTeamGameSystem): void {
        $this->twoTeamGameSystem = $twoTeamGameSystem;
    }

    public function join(Player $player) {
        $this->twoTeamGameSystem->join($player);
    }

    public function updateNameTag(Player $player) {
        $playerData = TeamSystem::getPlayerData($player->getName());
        if ($playerData->getBelongTeamId() !== null) {
            $game = $this->twoTeamGameSystem->getGame();
            NameTagController::update($player, $game->getRedTeamId());
        }
    }

    public function canReceiveDamage(Player $attacker, Player $victim): bool {
        return $this->twoTeamGameSystem->canReceiveDamage($attacker, $victim);
    }

    public function onDead(Player $attacker, Player $victim) {
        if ($this->twoTeamGameSystem instanceof TeamDeathMatchSystem) {
            $this->twoTeamGameSystem->addScore($attacker);
        }

        $this->sendKillMessage($attacker, $victim);
        $victim->setSpawn($victim->getPosition());

        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim);
        $cadaverEntity->spawnToAll();
    }

    public function returnToLobby(array $players) {
        foreach ($players as $player) {
            $level = $this->server->getLevelByName("lobby");
            $pos = $level->getSpawnLocation();
            $player->teleport($pos);
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
        $game = $this->twoTeamGameSystem->getGame();
        NameTagController::showToAlly($player, $game->getId(), $game->getRedTeamId(), $this->server);
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $player->getName()) {
                    $entity->kill();
                }
            }
        }
    }

    public function setInitInventory(Player $player): void {
        $playerData = MilitaryDepartmentSystem::getPlayerData($player->getName());
        /** @var GunData $mainGunData */
        $mainGunData = WeaponDataSystem::get($player->getName(), $playerData->getEquipMainGunName());
        /** @var GunData $subGunData */
        $subGunData = WeaponDataSystem::get($player->getName(), $playerData->getEquipSubGunName());
        $player->getInventory()->setContents([
            GunSystem::getGun($player, $mainGunData->getName(), $mainGunData->getScopeName()),
            GunSystem::getGun($player, $subGunData->getName(), $subGunData->getScopeName()),
        ]);
        $player->getInventory()->setItem(8, ItemFactory::get(ItemIds::ARROW, 0, 1));
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
        $this->twoTeamGameSystem->setSpawnPoint($player);
        if ($cadaverEntity !== null) $player->teleport($cadaverEntity->getPosition()->add(0, 1, 0));
        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player): void {
            if ($player->isOnline()) $player->getInventory()->addItem(new RespawnItem());
        }), 20 * 5);
    }

    public function sendKillMessage(Player $attacker, Player $victim): void {
        $attackerWeapon = $attacker->getInventory()->getItemInHand();
        $message = $attacker->getNameTag() . " " . $attackerWeapon->getCustomName() . " " . $victim->getNameTag();
        foreach ($attacker->getLevel()->getPlayers() as $player) {
            $player->sendMessage($message);
        }
    }

    public function isJurisdiction(Player $player): bool {
        return $player->getLevel()->getName() === $this->twoTeamGameSystem->getMap()->getName();
    }
}