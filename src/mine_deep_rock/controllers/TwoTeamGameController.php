<?php


namespace mine_deep_rock\controllers;


use gun_system\GunSystem;
use military_department_system\MilitaryDepartmentSystem;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\scoreboards\LobbyScoreboard;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use scoreboard_system\ScoreboardSystem;
use team_death_match_system\TeamDeathMatchSystem;
use team_system\TeamSystem;
use two_team_game_system\models\Map;
use two_team_game_system\models\TwoTeamGame;
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

    public function getGameData(): TwoTeamGame {
        return $this->twoTeamGameSystem->getGame();
    }

    public function getMap(): Map {
        return $this->twoTeamGameSystem->getMap();
    }

    public function isTeamDeathMatch(): bool {
        return $this->twoTeamGameSystem instanceof TeamDeathMatchSystem;
    }

    public function canReceiveDamage(Player $attacker, Player $victim): bool {
        return $this->twoTeamGameSystem->canReceiveDamage($attacker, $victim);
    }

    public function init(TwoTeamGameSystem $twoTeamGameSystem): void {
        $this->twoTeamGameSystem = $twoTeamGameSystem;
    }

    public function addScore(Player $player): void {
        $this->twoTeamGameSystem->addScore($player);
    }

    public function joinGame(Player $player) {
        $this->twoTeamGameSystem->join($player);
    }

    public function setSpawnPoint(Player $player): void {
        $this->twoTeamGameSystem->setSpawnPoint($player);
    }

    public function updateScoreboard(): void {
        $players = $this->server->getLevelByName($this->twoTeamGameSystem->getMap()->getName())->getPlayers();
        $participants = TeamSystem::getParticipantData($this->twoTeamGameSystem->getGame()->getId());

        $scoreboard = new LobbyScoreboard(count($participants));
        foreach ($players as $player) {
            ScoreboardSystem::updateScoreboard($player, $scoreboard);
        }
    }

    public function updateNameTag(Player $player) {
        $playerData = TeamSystem::getPlayerData($player->getName());
        if ($playerData->getBelongTeamId() !== null) {
            $game = $this->twoTeamGameSystem->getGame();
            NameTagController::update($player, $game->getRedTeamId());
        }
    }

    public function setEffects(Player $player): void {
        $playerData = MilitaryDepartmentSystem::getPlayerData($player->getName());
        foreach ($playerData->getMilitaryDepartment()->getEffects() as $effect) $player->addEffect($effect);
    }

    public function setEquipments(Player $player): void {
        $playerData = MilitaryDepartmentSystem::getPlayerData($player->getName());
        /** @var GunData $mainGunData */
        $mainGunData = WeaponDataSystem::get($player->getName(), $playerData->getEquipMainGunName());
        /** @var GunData $subGunData */
        $subGunData = WeaponDataSystem::get($player->getName(), $playerData->getEquipSubGunName());
        $items = [
            GunSystem::getGun($player, $mainGunData->getName(), $mainGunData->getScopeName()),
            GunSystem::getGun($player, $subGunData->getName(), $subGunData->getScopeName()),
        ];
        foreach ($playerData->getMilitaryDepartment()->getCanEquipGadgetsType() as $gadgetType) {
            $items[] = $gadgetType->toItem();
        }
        $player->getInventory()->setContents($items);
        $player->getInventory()->setItem(8, ItemFactory::get(ItemIds::ARROW, 0, 1));
    }

    public function killCadaverEntity(Player $owner): void {
        foreach ($owner->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $owner->getName()) {
                    $entity->kill();
                }
            }
        }
    }

    public function getCadaverEntity(Player $player): ?CadaverEntity {
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof CadaverEntity) {
                if ($entity->getOwner()->getName() === $player->getName()) {
                    return $entity;
                }
            }
        }

        return null;
    }

    public function sendKillMessage(Player $attacker, Player $victim): void {
        $attackerWeapon = $attacker->getInventory()->getItemInHand();
        $message = $attacker->getNameTag() . " " . $attackerWeapon->getCustomName() . " " . $victim->getNameTag();
        foreach ($attacker->getLevel()->getPlayers() as $player) {
            $player->sendMessage($message);
        }
    }

    public function returnToLobby(array $players) {
        foreach ($players as $player) {
            $level = $this->server->getLevelByName("lobby");
            $pos = $level->getSpawnLocation();
            $player->teleport($pos);
        }
    }
}