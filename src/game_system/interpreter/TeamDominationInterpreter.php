<?php


namespace game_system\interpreter;


use game_system\model\Coordinate;
use game_system\model\map\DominationFlag;
use game_system\model\SpawnBeacon;
use game_system\model\User;
use game_system\pmmp\Entity\SpawnBeaconEntity;
use game_system\pmmp\form\SpawnPointSelectForm;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class TeamDominationInterpreter extends TwoTeamGameInterpreter
{

    private $flagSchedulerHandler;

    public function start(): bool {
        foreach ($this->getGameData()->getMap()->getFlags() as $flag) {
            $flag->setOnOccupied(function ($name, $gauge) {
                if ($gauge > 0) $this->client->changeColorRed($name);
                if ($gauge < 0) $this->client->changeColorBlue($name);
            });

            $this->client->spawnFlag(
                $flag->getName(),
                Server::getInstance()->getLevelByName($this->getGameData()->getMap()->getName()),
                new Vector3(
                    $flag->getCenter()->getX(),
                    $flag->getCenter()->getY(),
                    $flag->getCenter()->getZ()
                ));
        }

        $this->flagSchedulerHandler = $this->scheduler->scheduleRepeatingTask(new ClosureTask(function (int $tick): void {
            foreach ($this->getGameData()->getMap()->getFlags() as $flag) {
                if ($flag->isOccupied()) $this->addScoreByFlag($flag);
                $this->makeFlagProgress($flag);
            }
            $this->updateFlagStatus($this->getGameData()->getMap()->getFlags());
        }), 20 * 1);

        return parent::start();
    }

    protected function onFinished(): void {
        $this->client->removeAllFlags();
        $this->flagSchedulerHandler->cancel();
        parent::onFinished();
    }

    public function makeFlagProgress(DominationFlag $flag): void {
        //TODO:リファクタリング
        $flagAroundStatus = $this->getFlagAroundTeam(new Vector3(
            $flag->getCenter()->getX(),
            $flag->getCenter()->getY(),
            $flag->getCenter()->getZ()
        ));

        $teamId = $flagAroundStatus[0];
        $teamPlayers = $flagAroundStatus[1];

        if ($teamId === null) return;
        if ($teamId->equal($this->getGameData()->getRedTeam()->getId())) {
            $i = 0;
            foreach ($teamPlayers as $player) {
                if ($i <= 4) {
                    $result = $flag->makeProgressByRed();
                    if ($result) {
                        $this->gameScoresService->addPoint($player->getName(), $this->getGameData()->getId(), 10);
                        $player->sendPopup($player->getName() . "拠点を占領中+10");
                    }
                    $i++;
                }
            }
        } else {
            $i = 0;
            foreach ($teamPlayers as $player) {
                if ($i <= 4) {
                    $result = $flag->makeProgressByBlue();
                    if ($result) {
                        $this->gameScoresService->addPoint($player->getName(), $this->getGameData()->getId(), 10);
                        $player->sendPopup($player->getName() . "拠点を占領中+10");
                    }
                    $i++;
                }
            }
        }
    }

    public function addScoreByFlag(DominationFlag $flag): void {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());
        $players = $level->getPlayers();
        if ($flag->isRedTeams()) {
            foreach ($players as $player) {
                $this->game->redTeamScore += intval($flag->getGauge() / 10);
                $this->client->updateRedTeamScoreboard($player, $this->game->redTeamScore);
            }
        } else if ($flag->isBlueTeams()) {
            foreach ($players as $player) {
                $this->game->blueTeamScore += intval(-$flag->getGauge() / 10);
                $this->client->updateBlueTeamScoreboard($player, $this->game->blueTeamScore);
            }
        }
    }

    public function updateFlagStatus(array $flags): void {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());
        $players = $level->getPlayers();
        $this->client->updateFlagStatus($players, $flags);
    }

    //TODO:リファクタリング
    private function getFlagAroundTeam(Vector3 $flagPosition): array {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());

        $players = $level->getPlayers();
        $redTeamPlayers = [];
        $blueTeamPlayers = [];
        foreach ($players as $player) {
            if ($flagPosition->distance($player->getPosition()) <= 8 && $player->getGamemode() === Player::ADVENTURE) {
                $playerTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
                if ($playerTeamId->equal($this->getGameData()->getRedTeam()->getId())) {
                    $redTeamPlayers[] = $player;
                } else {
                    $blueTeamPlayers[] = $player;
                }
            }
        }

        if (count($redTeamPlayers) > count($blueTeamPlayers)) {
            for ($i = 0; $i <= count($blueTeamPlayers); ++$i) {
                array_splice($redTeamPlayers, $i, $i);
            }
            return [$this->getGameData()->getRedTeam()->getId(), $redTeamPlayers];
        } else if (count($redTeamPlayers) === count($blueTeamPlayers)) {
            return [null, []];
        } else {
            for ($i = 0; $i <= count($redTeamPlayers); ++$i) {
                array_splice($blueTeamPlayers, $i, $i);
            }
            return [$this->getGameData()->getBlueTeam()->getId(), $blueTeamPlayers];
        }
    }

    protected function onDead(Player $attackerPlayer, string $attackerWeaponName, Player $targetPlayer, User $attackerUser, User $targetUser): void {
        $this->gameScoresService->addKillCount($attackerUser->getName(), $this->getGameData()->getId());
        $this->gameScoresService->addPoint($attackerUser->getName(), $this->getGameData()->getId(), 2);

        parent::onDead($attackerPlayer, $attackerWeaponName, $targetPlayer, $attackerUser, $targetUser);
    }

    public function spawn(User $user, ?Coordinate $spawnPoint = null): void {
        if ($spawnPoint !== null) {
            parent::spawn($user, $spawnPoint);
            return;
        }

        $player = Server::getInstance()->getPlayer($user->getName());
        $userTeamId = $user->getBelongTeamId();

        $flags = [];

        foreach ($this->getGameData()->getMap()->getFlags() as $flag) {
            if ($flag->isOccupied()) {
                if ($this->getGameData()->getRedTeam()->getId()->equal($userTeamId)) {
                    if ($flag->isRedTeams()) $flags[] = $flag;
                } else {
                    if ($flag->isBlueTeams()) $flags[] = $flag;
                }
            }
        }

        $spawnBeacons = [];
        foreach ($player->getLevel()->getEntities() as $entity) {
            if ($entity instanceof SpawnBeaconEntity) {
                $spawnBeacon = $entity->getSpawnBeaconData();
                if ($spawnBeacon->getOwnerTeamId()->equal($user->getBelongTeamId())) {
                    $nearFlagName = "";
                    $pos = $spawnBeacon->getPosition()->toVector3();
                    $distance = null;

                    foreach ($this->getGameData()->getMap()->getFlags() as $flag) {
                        if ($distance === null) {
                            $nearFlagName = $flag->getName();
                            $distance = $flag->getCenter()->toVector3()->distance($pos);
                        } else if ($distance > $flag->getCenter()->toVector3()->distance($pos)) {
                            $nearFlagName = $flag->getName();
                            $distance = $flag->getCenter()->toVector3()->distance($pos);
                        }
                    }

                    $spawnBeacon->setDescribe($spawnBeacon->getOwnerName() . ":" . $nearFlagName . " " . intval($distance) . "m");
                    $spawnBeacons[] = $spawnBeacon;
                }
            };
        }

        if (count($flags) === 0 && count($spawnBeacons) === 0) {
            parent::spawn($user);
            return;
        }

        $player->sendForm(new SpawnPointSelectForm(function ($flagOrBeacon) use ($user, $userTeamId) {
            if ($flagOrBeacon === null) {
                parent::spawn($user);
                return;
            }

            if ($flagOrBeacon instanceof DominationFlag) {
                if (!$flagOrBeacon->isOccupied()) {
                    parent::spawn($user);
                    return;
                }

                if ($this->getGameData()->getRedTeam()->getId()->equal($userTeamId)) {
                    if ($flagOrBeacon->isRedTeams()) {
                        parent::spawn($user, $flagOrBeacon->getCenter());
                        return;
                    }
                } else {
                    if ($flagOrBeacon->isBlueTeams()) {
                        parent::spawn($user, $flagOrBeacon->getCenter());
                        return;
                    }
                }
            }

            if ($flagOrBeacon instanceof SpawnBeacon) {
                if (!$flagOrBeacon->isAvailable()) {
                    parent::spawn($user);
                    return;
                }
                parent::spawn($user, $flagOrBeacon->getPosition());
                return;
            }
        }, $flags, $spawnBeacons));
    }
}