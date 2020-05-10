<?php


namespace game_system\interpreter;


use easy_scoreboard_api\EasyScoreboardAPI;
use game_system\model\map\DominationFlag;
use game_system\model\TeamId;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class TeamDominationInterpreter extends TwoTeamGameInterpreter
{

    private $flagSchedulerHandler;

    public function start(): bool {
        foreach ($this->getGameData()->getMap()->getFlags() as $flag) {
            $flag->setOnOccupied(function ($name,$gauge) {
                var_dump($name);
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
                if ($flag->isOccupied()) {
                    $this->addScoreByFlag($flag);
                } else {
                    $this->makeFlagProgress($flag);
                }
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
        $teamId = $this->getFlagAroundTeam(new Vector3(
            $flag->getCenter()->getX(),
            $flag->getCenter()->getY(),
            $flag->getCenter()->getZ()
        ));

        if ($teamId === null) return;
        if ($teamId->equal($this->getGameData()->getRedTeam()->getId())) {
            $flag->makeProgressByRed();
        } else {
            $flag->makeProgressByBlue();
        }
    }

    public function addScoreByFlag(DominationFlag $flag): void {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());
        $players = $level->getPlayers();
        if ($flag->isRedTeams()) {
            foreach ($players as $player){
                $this->client->updateRedTeamScoreboard($player,++$this->game->redTeamScore);
            }
        } else {
            foreach ($players as $player){
                $this->client->updateBlueTeamScoreboard($player,++$this->game->blueTeamScore);
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

    private function getFlagAroundTeam(Vector3 $flagPosition): ?TeamId {
        $level = Server::getInstance()->getLevelByName($this->game->getMap()->getName());

        $aroundRedPlayers = 0;
        $aroundBluePlayers = 0;
        $players = $level->getPlayers();
        foreach ($players as $player) {
            if ($flagPosition->distance($player->getPosition()) <= 15) {
                $playerTeamId = $this->usersService->getUserData($player->getName())->getBelongTeamId();
                if ($playerTeamId->equal($this->getGameData()->getRedTeam()->getId())) {
                    $aroundRedPlayers++;
                } else {
                    $aroundBluePlayers++;
                }
            }
        }

        if ($aroundRedPlayers > $aroundBluePlayers) {
            return $this->getGameData()->getRedTeam()->getId();
        } else if ($aroundRedPlayers === $aroundBluePlayers) {
            return null;
        } else {
            return $this->getGameData()->getBlueTeam()->getId();
        }
    }
}