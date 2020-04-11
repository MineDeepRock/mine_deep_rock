<?php

namespace team_system\service;

use Service;
use ServiceErrorMessage;
use ServiceResult;
use team_system\models\Player;
use team_system\models\Team;
use team_system\models\TeamId;
use team_system\repository\TeamRepository;

class TeamService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new TeamRepository();

    }

    /**
     * @param Player $owner
     * @return bool
     */
    public function contain(Player $owner): bool {
        return $this->repository->contain($owner->getName());
    }

    private function searchAtOwner(Player $owner): ?Team {
        return $this->repository->searchAtOwnerName($owner->getName());
    }

    private function searchAtId(?TeamId $id): ?Team {
        if ($id === null)
            return null;
        return $this->repository->searchAtId($id->value());
    }


    /**
     * @param Player $owner
     * @return ServiceResult
     */
    public function create(Player $owner): ServiceResult {

        if ($this->contain($owner)) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにチームを作っています"));

        } else if ($owner->getBelongTeamId() !== null) {
            return new ServiceResult(false, new ServiceErrorMessage("すでに他のチームに参加しています"));

        } else {
            $createdTeam = Team::asNew($owner);

            $this->repository->create($createdTeam->getId()->value(), $owner->getName());
            return new ServiceResult(true, $createdTeam);
        }
    }

    /**
     * @param Player $sender
     * @param String $ownerName
     * @return ServiceResult
     */
    public
    function join(Player $sender, String $ownerName): ServiceResult {

        $team = $this->repository->searchAtOwnerName($ownerName);

        if ($team === null)
            return new ServiceResult(false, new ServiceErrorMessage("そのようなチームは存在しません"));

        if ($team->isFull())
            return new ServiceResult(false, new ServiceErrorMessage("そのチームは満員です"));

        if ($sender->getBelongTeamId() === null) {
            $this->repository->join($sender->getName(), $team);

            return new ServiceResult(true, $team);
        }

        $playerBelongTheTeam = $sender->getBelongTeamId()->equal($team->getId());

        if ($playerBelongTheTeam) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにそのチームに参加しています"));

        } else {
            return new ServiceResult(false, new ServiceErrorMessage("あなたは他のチームに参加しています"));

        }
    }


    /**
     * @param Player $sender
     * @param TeamId|null $teamId
     * @return ServiceResult
     */
    public
    function quit(Player $sender, ?TeamId $teamId): ServiceResult {

        $team = $this->searchAtId($teamId);

        if ($team === null) {
            return new ServiceResult(false, new ServiceErrorMessage("あなたはチームに参加していません"));

        } else if ($team->getOwner()->getName() === $sender->getName()) {
            return $this->yieldOwner($sender);

        } else {
            $this->repository->quit($sender->getName(), $team);
            return new ServiceResult(true);
        }
    }

    public function yieldOwner(Player $currentOwner, string $nextOwnerName = null): ServiceResult {
        if (!$this->repository->contain($currentOwner->getName()))
            return new ServiceResult(false, new ServiceErrorMessage("あなたはオーナで無いか、チームに入っていません"));

        $team = $this->searchAtOwner($currentOwner);

        if ($team->isEmpty()) {
            $this->repository->delete($currentOwner->getName());
            return new ServiceResult(true);

        } else if ($nextOwnerName === null) {
            $nextOwnerName = $team->getCoworkersName()[0];
            $this->repository->yieldOwner($currentOwner->getName(), $nextOwnerName, $team->isWherePlayerSlot($nextOwnerName));
            return new ServiceResult(true);

        } else if (in_array($nextOwnerName, $team->getCoworkersName())) {
            $this->repository->yieldOwner($currentOwner->getName(), $nextOwnerName, $team->isWherePlayerSlot($nextOwnerName));
            return new ServiceResult(true);

        } else {
            return new ServiceResult(false, new ServiceErrorMessage("そのような名前のプレイヤーはチームにいません"));
        }

    }
}