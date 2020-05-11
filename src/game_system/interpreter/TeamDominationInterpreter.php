<?php


namespace game_system\interpreter;


use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\map\DominationFlag;
use game_system\model\TeamId;
use game_system\model\User;
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
            $result = $flag->makeProgressByRed();
        } else {
            $result = $flag->makeProgressByBlue();
        }

        if ($result) {
            foreach ($teamPlayers as $player) {
                $this->gameScoresService->addPoint($player->getName(),$this->getGameData()->getId(),10);
                $player->sendPopup($player->getName() . "拠点を占領中+10");
            }
        }
    }

    public function addScoreByFlag(DominationFlag $flag): void {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());
        $players = $level->getPlayers();
        if ($flag->isRedTeams()) {
            foreach ($players as $player) {
                $this->game->redTeamScore += intval($flag->getGauge()/10);
                $this->client->updateRedTeamScoreboard($player, $this->game->redTeamScore);
            }
        } else if ($flag->isBlueTeams()) {
            foreach ($players as $player) {
                $this->game->blueTeamScore += intval(-$flag->getGauge()/10);
                $this->client->updateBlueTeamScoreboard($player, $this->game->blueTeamScore);
            }
        }
    }

    public function updateFlagStatus(array $flags): void {
        $api = EasyScoreboardAPI::getInstance();
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());

        $players = $level->getPlayers();
        foreach ($players as $player) {
            $index = 4;
            foreach ($flags as $flag) {
                $api->removeScore($player, "sidebar", $index);
                $api->setScore($player, "sidebar", $flag->toString(), $index, $index);
                $index++;
            }
        }
    }

    //TODO:リファクタリング
    private function getFlagAroundTeam(Vector3 $flagPosition): array {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());

        $players = $level->getPlayers();
        $redTeamPlayers = [];
        $blueTeamPlayers = [];
        foreach ($players as $player) {
            if ($flagPosition->distance($player->getPosition()) <= 8) {
                $playerTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
                if ($playerTeamId->equal($this->getGameData()->getRedTeam()->getId())) {
                    $redTeamPlayers[] = $player;
                } else {
                    $blueTeamPlayers[] = $player;
                }
            }
        }

        if (count($redTeamPlayers) > count($blueTeamPlayers)) {
            return [$this->getGameData()->getRedTeam()->getId(),$redTeamPlayers];
        } else if (count($redTeamPlayers) === count($blueTeamPlayers)) {
            return [null,[]];
        } else {
            return [$this->getGameData()->getBlueTeam()->getId(),$blueTeamPlayers];
        }
    }

    protected function onDead(Player $attackerPlayer, string $attackerWeaponName, Player $targetPlayer, User $attackerUser, User $targetUser): void {
        $this->gameScoresService->addKillCount($attackerUser->getName(),$this->getGameData()->getId());
        $this->gameScoresService->addPoint($attackerUser->getName(),$this->getGameData()->getId(),2);

        parent::onDead($attackerPlayer, $attackerWeaponName, $targetPlayer, $attackerUser, $targetUser);
    }
}