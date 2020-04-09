<?php

namespace team_system\service;

use ServiceErrorMessage;
use ServiceResult;
use team_system\models\Player;
use team_system\models\Team;
use team_system\models\TeamId;
use team_system\repository\TeamRepository;

class TeamService
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

    public function searchAtOwner(Player $owner): ?Team {
        return $this->repository->searchAtOwnerName($owner->getName());
    }

    public function searchAtId(TeamId $id): ?Team {
        return $this->repository->searchAtId($id->value());
    }

    /**
     * @param Player $owner
     * @return ServiceResult
     */
    public function create(Player $owner): ServiceResult {

        if ($this->contain($owner)) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにチームを作っています"));
        } else if ($owner->getBelongTeamId()->value() !== null) {
            return new ServiceResult(false, new ServiceErrorMessage("すでに他のチームに参加しています"));
        } else {
            $createdTeam = Team::asNew($owner);

            $this->repository->create($createdTeam->getId(), $owner->getName());
            return new ServiceResult(true, $createdTeam);
        }
    }

    /**
     * @param Player $sender
     * @param String $nameOrId
     * @return ServiceResult
     */
    public
    function join(Player $sender, String $nameOrId): ServiceResult {

        $team = $this->repository->searchAtOwnerName($nameOrId) ?? $this->repository->searchAtId($nameOrId);

        if ($team === null) {
            return new ServiceResult(false, new ServiceErrorMessage("そのようなチームは存在しません"));
        } else if ($sender->getBelongTeamId()->value() != null) {
            return new ServiceResult(false, new ServiceErrorMessage("あなたは他のチームに参加しています"));
        } else if ($sender->getBelongTeamId() === $team->getId()) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにそのチームに参加しています"));
        } else if ($team->isFull()) {
            return new ServiceResult(false, new ServiceErrorMessage("そのチームは満員です"));
        } else {
            $this->repository->join($sender->getName(), $team);

            return new ServiceResult(true, $team);
        }
    }

    /**
     * @param Player $sender
     * @param String $nameOrId
     * @return ServiceResult
     */
    public
    function quit(Player $sender, String $nameOrId): ServiceResult {

        $team = $this->repository->searchAtOwnerName($nameOrId) ?? $this->repository->searchAtId($nameOrId);

        if ($team === null) {
            return new ServiceResult(false, new ServiceErrorMessage("あなたはチームに参加していません"));

        } else if ($team->getOwner() == $sender->getName()) {
            $this->yieldOwner($sender);
            return new ServiceResult(true);

        } else {
            $this->repository->quit($sender->getName(), $team);
            return new ServiceResult(true);
        }
    }

    public function yieldOwner(Player $currentOwner, string $nextOwnerName = null): ServiceResult {
        if (!$this->repository->contain($currentOwner->getName())) {
            return new ServiceResult(false, new ServiceErrorMessage("あなたはオーナで無いか、チームに入っていません"));
        }

        $team = $this->repository->searchAtOwnerName($currentOwner->getName());

        if ($team->isEmpty()) {
            $this->repository->delete($currentOwner);
            return new ServiceResult(true);

        } else if ($nextOwnerName === null) {
            $nextOwnerName = $team->getCoworkersName()[0];
            $this->repository->yieldOwner($currentOwner, $nextOwnerName, $team->isWherePlayerSlot($nextOwnerName));
            return new ServiceResult(true);

        } else if (in_array($nextOwnerName, $team->getCoworkersName())) {
            $this->repository->yieldOwner($currentOwner, $nextOwnerName, $team->isWherePlayerSlot($nextOwnerName));
            return new ServiceResult(true);

        } else {
            return new ServiceResult(false, new ServiceErrorMessage("そのような名前のプレイヤーはチームにいません"));
        }

    }

    public
    function breakup() {
        // TODO: Implement breakup() method.
    }
}