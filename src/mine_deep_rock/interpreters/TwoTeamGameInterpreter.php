<?php


namespace mine_deep_rock\interpreters;


use gun_system\GunSystem;
use gun_system\pmmp\items\ItemGun;
use military_department_system\MilitaryDepartmentSystem;
use military_department_system\models\NursingSoldier;
use mine_deep_rock\controllers\NameTagController;
use mine_deep_rock\controllers\TwoTeamGameController;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\pmmp\items\RespawnItem;
use mine_deep_rock\scoreboards\LobbyScoreboard;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use scoreboard_system\ScoreboardSystem;
use team_system\TeamSystem;
use two_team_game_system\TwoTeamGameSystem;

class TwoTeamGameInterpreter
{
    private $controller;
    private $server;
    private $scheduler;

    public function __construct(TwoTeamGameSystem $twoTeamGameSystem, Server $server, TaskScheduler $scheduler) {
        $this->server = $server;
        $this->scheduler = $scheduler;
        $this->controller = new TwoTeamGameController($twoTeamGameSystem, $this->server, $this->scheduler);
    }

    public function joinGame(Player $player) {
        $this->controller->joinGame($player);
        $this->controller->updateScoreboard();
        $this->controller->setSpawnPoint($player);
        if ($this->controller->getGameData()->isStarted()) {
            $this->spawn($player);
        }
    }

    public function onRegainHealth(Player $player) {
        $this->controller->updateNameTag($player);
    }

    public function onJoinServer(Player $player) {
        $participants = TeamSystem::getParticipantData($this->controller->getGameData()->getId());
        ScoreboardSystem::setScoreboard($player->getPlayer(), new LobbyScoreboard(count($participants)));
    }

    public function onReceiveDamage(Player $attacker, Player $victim, float $damage): bool {
        if ($this->isJurisdiction($victim)) {
            if (!$this->controller->canReceiveDamage($attacker, $victim)) {
                return false;
            }

            if ($attacker->getInventory()->getItemInHand() instanceof ItemGun) {
                $isFinisher = $victim->getHealth() - $damage <= 0;
                GunSystem::sendHitMessage($attacker, $isFinisher);
                GunSystem::sendHitParticle($victim->getLevel(), $victim->getPosition(), $damage, $isFinisher);
            }
            $this->controller->updateNameTag($attacker);
        }

        return true;
    }

    public function onDead(Player $attacker, Player $victim) {
        if ($this->controller->isTeamDeathMatch()) {
            $this->controller->addScore($attacker);
        }

        $this->controller->sendKillMessage($attacker, $victim);
        $victim->setSpawn($victim->getPosition());

        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim);
        $cadaverEntity->spawnToAll();
    }

    public function onGameFinish(array $players): void {
        $this->controller->returnToLobby($players);
    }

    public function onPlayerRespawn(Player $player): void {
        if ($this->isJurisdiction($player)) {
            $this->displayDeathScreen($player);
        }
    }

    public function spawn(Player $player, Vector3 $position = null): void {
        $player->setGamemode(Player::ADVENTURE);
        $player->setImmobile(false);
        $player->teleport($position ?? $player->getSpawn());
        $this->controller->setSpawnPoint($player);

        $this->controller->setEffects($player);
        $this->controller->setEquipments($player);
        $this->controller->killCadaverEntity($player);

        $game = $this->controller->getGameData();
        NameTagController::showToAlly($player, $game->getId(), $game->getRedTeamId(), $this->server);
    }

    public function displayDeathScreen(Player $player): void {
        $cadaverEntity = $this->controller->getCadaverEntity($player);

        $player->getInventory()->setContents([]);
        $player->setGamemode(Player::SPECTATOR);
        $player->setImmobile(true);
        $this->controller->setSpawnPoint($player);

        if ($cadaverEntity !== null) $player->teleport($cadaverEntity->getPosition()->add(0, 1, 0));

        $this->scheduler->scheduleDelayedTask(new ClosureTask(function (int $tick) use ($player): void {
            if ($player->isOnline()) $player->getInventory()->addItem(new RespawnItem());
        }), 20 * 5);
    }

    public function useRespawnItem(Player $player): void {
        $this->spawn($player);
    }

    public function resuscitate(Player $player, CadaverEntity $cadaver): void {
        if (!$cadaver->getOwner()->isOnline()) return;

        $playerData = TeamSystem::getPlayerData($player->getName());
        $cadaverData = TeamSystem::getPlayerData($cadaver->getOwner()->getName());

        if ($playerData->getBelongTeamId() === null || $cadaverData->getBelongTeamId() == null) return;

        if ($playerData->getBelongTeamId()->equal($cadaverData->getBelongTeamId())) {
            $department = MilitaryDepartmentSystem::getPlayerData($player->getName())->getMilitaryDepartment();
            if ($department->equal(new NursingSoldier())) {
                $this->spawn($cadaver->getOwner(), $cadaver->getPosition());
            }
        }

    }

    public function isJurisdiction(Player $player) {
        return $player->getLevel()->getName() === $this->controller->getMap()->getName();
    }
}