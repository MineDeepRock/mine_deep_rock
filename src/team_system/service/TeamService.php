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
    private function contain(Player $owner): bool {
        return $this->repository->contain($owner->getName());
    }

    /**
     * @param Player $owner
     * @return ServiceResult
     */
    public function create(Player $owner): ServiceResult {

        if ($this->contain($owner)) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにチームを作っています"));
        } else if ($owner->getBelongTeamId() != null) {
            return new ServiceResult(false, new ServiceErrorMessage("すでに他のチームに参加しています"));
        } else {
            $createdTeam = Team::asNew($owner);
            $owner->setBelongTeamId($createdTeam->getId());

            $this->repository->create($createdTeam);
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

        $team = $this->repository->search($ownerName);

        if ($sender->getBelongTeamId() === $team->getId()) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにそのチームに参加しています"));
        } else if ($sender->getBelongTeamId() != null) {
            return new ServiceResult(false, new ServiceErrorMessage("あなたは他のチームに参加しています"));
        } else if ($team == null) {
            return new ServiceResult(false, new ServiceErrorMessage("そのようなチームは存在しません"));
        } else if ($team->isFull()) {
            return new ServiceResult(false, new ServiceErrorMessage("そのチームは満員です"));
        } else {
            $this->repository->join($sender, $team);
            $sender->setBelongTeamId($team->getId());
            //TODO:参加したチームデータを返すようにする
            return new ServiceResult(true, "参加しました");
        }
    }

    /**
     * @param Player $sender
     * @param String $ownerName
     * @return ServiceResult
     */
    public
    function quit(Player $sender, String $ownerName): ServiceResult {

        $team = $this->repository->search($ownerName);

        if ($sender->getBelongTeamId() == null) {
            return new ServiceResult(false, new ServiceErrorMessage("あなたはチームに参加していません"));
        } else if ($team == null) {
            return new ServiceResult(false, new ServiceErrorMessage("そのようなチームは存在しません"));
        } else {
            $this->repository->quit($sender, $team);
            $sender->setBelongTeamId(null);
            return new ServiceResult(true, null);
        }
    }

    public
    function breakup() {
        // TODO: Implement breakup() method.
    }
}