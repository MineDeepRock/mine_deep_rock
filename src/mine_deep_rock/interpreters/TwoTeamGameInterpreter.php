<?php


namespace mine_deep_rock\interpreters;


use gun_system\GunSystem;
use gun_system\pmmp\items\ItemGun;
use military_department_system\MilitaryDepartmentSystem;
use military_department_system\models\NursingSoldier;
use mine_deep_rock\controllers\TwoTeamGamePlayerController;
use mine_deep_rock\controllers\TwoTeamNameTagController;
use mine_deep_rock\listeners\BoxListener;
use mine_deep_rock\pmmp\entities\CadaverEntity;
use mine_deep_rock\pmmp\items\RespawnItem;
use mine_deep_rock\scoreboards\LobbyScoreboard;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_death_match_system\TeamDeathMatchSystem;
use team_system\TeamSystem;
use two_team_game_system\TwoTeamGameSystem;

class TwoTeamGameInterpreter
{
    private $twoTeamGameSystem;

    private $server;
    private $scheduler;

    public function __construct(TwoTeamGameSystem $twoTeamGameSystem, Server $server, TaskScheduler $scheduler) {
        $this->server = $server;
        $this->scheduler = $scheduler;
        $this->twoTeamGameSystem = $twoTeamGameSystem;
        BoxListener::setGame($this->twoTeamGameSystem->getGame());
    }

    public function joinGame(Player $player) {
        $this->twoTeamGameSystem->join($player);
        $this->twoTeamGameSystem->setSpawnPoint($player);
        $this->updateScoreboard();
        if ($this->twoTeamGameSystem->getGame()->isStarted()) {
            $this->spawn($player);
        }
    }

    public function quitGame(Player $player) {
        $this->twoTeamGameSystem->quit($player);
        $this->updateScoreboard();
    }

    public function onGameStart(array $players) {
        foreach ($players as $player) {
            if (!$player->isOnline()) continue;
            TwoTeamNameTagController::set($player, $this->twoTeamGameSystem->getGame());
            $this->spawn($player);
            $player->setNameTag("");
            //$player->setNameTagVisible(false);うまく行かない
        }
    }

    public function onJoinServer(Player $player) {
        $participants = TeamSystem::getParticipantData($this->twoTeamGameSystem->getGame()->getId());
        LobbyScoreboard::send($player, count($participants));
    }

    public function onRegainHealth(Player $player) {
        TwoTeamNameTagController::update($player, $this->twoTeamGameSystem->getGame());
    }

    public function canReceiveDamage(Player $attacker, Player $victim) {
        return $this->twoTeamGameSystem->canReceiveDamage($attacker, $victim);
    }

    public function onReceiveDamage(Player $player): void {
        TwoTeamNameTagController::update($player, $this->twoTeamGameSystem->getGame());
    }

    public function onReceiveDamageByPlayer(Player $attacker, Player $victim, float $damage): void {
        if ($attacker->getInventory()->getItemInHand() instanceof ItemGun) {
            $isFinisher = $victim->getHealth() - $damage <= 0;
            GunSystem::sendHitMessage($attacker, $isFinisher);
            GunSystem::sendHitParticle($victim->getLevel(), $victim->getPosition(), $damage, $isFinisher);
        }
        TwoTeamNameTagController::update($victim, $this->twoTeamGameSystem->getGame());
    }

    public function onKilledPlayer(Player $attacker, Player $victim) {
        if ($this->twoTeamGameSystem instanceof TeamDeathMatchSystem) {
            $this->twoTeamGameSystem->addScore($attacker);
        }

        $victim->setSpawn($victim->getPosition());

        $this->sendPlayersKillMessage($attacker, $victim);
        $this->spawnCadaverEntity($victim);
    }

    public function sendPlayersKillMessage(Player $attacker, Player $victim): void {
        $attackerWeapon = $attacker->getInventory()->getItemInHand();
        $message = $attacker->getNameTag() . " " . $attackerWeapon->getCustomName() . " " . $victim->getNameTag();
        foreach ($attacker->getLevel()->getPlayers() as $player) {
            $player->sendMessage($message);
        }
    }

    public function spawnCadaverEntity(Player $victim) {
        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim);
        $cadaverEntity->spawnToAll();
    }

    public function returnToLobby(array $players): void {
        foreach ($players as $player) {
            $level = $this->server->getLevelByName("lobby");
            $pos = $level->getSpawnLocation();
            $player->teleport($pos);
        }
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
        $this->twoTeamGameSystem->setSpawnPoint($player);

        TwoTeamGamePlayerController::setEffects($player);
        TwoTeamGamePlayerController::setEquipments($player);
        TwoTeamGamePlayerController::removeCadaverEntity($player);

        TwoTeamNameTagController::showToAlly($player, $this->twoTeamGameSystem->getGame());
    }

    public function displayDeathScreen(Player $player): void {
        $cadaverEntity = TwoTeamGamePlayerController::getCadaverEntity($player);

        $player->getInventory()->setContents([]);
        $player->setGamemode(Player::SPECTATOR);
        $player->setImmobile(true);
        $this->twoTeamGameSystem->setSpawnPoint($player);

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

    private function updateScoreboard(): void {
        $participants = TeamSystem::getParticipantData($this->twoTeamGameSystem->getGame()->getId());
        foreach ($participants as $participant) {
            $player = $this->server->getPlayer($participant->getName());
            if ($player->isOnline()) {
                LobbyScoreboard::update($player, count($participants));
            }
        }
    }

    public function isJurisdiction(Player $player) {
        return $player->getLevel()->getName() === $this->twoTeamGameSystem->getMap()->getName();
    }
}