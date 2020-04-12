<?php

namespace team_system\services;

use Service;
use ServiceErrorMessage;
use ServiceResult;
use team_system\models\Member;
use team_system\models\Team;
use team_system\models\TeamId;
use team_system\repositories\TeamRepository;

class TeamService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new TeamRepository();

    }

    /**
     * @param Member $leader
     * @return bool
     */
    public function contain(Member $leader): bool {
        return $this->repository->contain($leader->getName());
    }

    private function searchAtLeader(Member $leader): ?Team {
        return $this->repository->searchAtLeaderName($leader->getName());
    }

    private function searchAtId(?TeamId $id): ?Team {
        if ($id === null)
            return null;
        return $this->repository->searchAtId($id->value());
    }


    /**
     * @param Member $leader
     * @return ServiceResult
     */
    public function create(Member $leader): ServiceResult {

        if ($this->contain($leader)) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにチームを作っています"));

        } else if ($leader->getBelongTeamId() !== null) {
            return new ServiceResult(false, new ServiceErrorMessage("すでに他のチームに参加しています"));

        } else {
            $createdTeam = Team::asNew($leader->getName());

            $this->repository->create($createdTeam->getId()->value(), $leader->getName());
            return new ServiceResult(true, $createdTeam);
        }
    }

    /**
     * @param Member $sender
     * @param String $leaderName
     * @return ServiceResult
     */
    public
    function join(Member $sender, String $leaderName): ServiceResult {

        $team = $this->repository->searchAtLeaderName($leaderName);

        if ($team === null)
            return new ServiceResult(false, new ServiceErrorMessage("そのようなチームは存在しません"));

        if ($team->isFull())
            return new ServiceResult(false, new ServiceErrorMessage("そのチームは満員です"));

        if ($sender->getBelongTeamId() === null) {
            $this->repository->join($sender->getName(), $team);

            return new ServiceResult(true, $team);
        }

        $alreadyJoined = $sender->getBelongTeamId()->equal($team->getId());

        if ($alreadyJoined) {
            return new ServiceResult(false, new ServiceErrorMessage("すでにそのチームに参加しています"));

        } else {
            return new ServiceResult(false, new ServiceErrorMessage("あなたは他のチームに参加しています"));

        }
    }


    /**
     * @param Member $sender
     * @return ServiceResult
     */
    public
    function quit(Member $sender): ServiceResult {

        $team = $this->searchAtId($sender->getBelongTeamId());

        if ($team === null) {
            return new ServiceResult(false, new ServiceErrorMessage("あなたはチームに参加していません"));

        } else if ($team->getLeaderName() === $sender->getName()) {
            $this->yieldLeader($sender);

            //譲ったあとのTeamデータ
            $team = $this->searchAtId($team->getId());
            $this->repository->quit($sender->getName(), $team);
            return new ServiceResult(true);

        } else {
            $this->repository->quit($sender->getName(), $team);
            return new ServiceResult(true);
        }
    }

    public function yieldLeader(Member $currentLeader, string $nextLeaderName = null): ServiceResult {
        if (!$this->repository->contain($currentLeader->getName()))
            return new ServiceResult(false, new ServiceErrorMessage("あなたはオーナで無いか、チームに入っていません"));

        $team = $this->searchAtLeader($currentLeader);

        if ($team->isEmpty()) {
            $this->repository->delete($currentLeader->getName());
            return new ServiceResult(true);

        } else if ($nextLeaderName === null) {
            $nextLeaderName = $team->getCoworkersName()[0];
            $this->repository->yieldLeader($currentLeader->getName(), $nextLeaderName, $team->getMemberSlot($nextLeaderName));
            return new ServiceResult(true);

        } else if (in_array($nextLeaderName, $team->getCoworkersName())) {
            $this->repository->yieldLeader($currentLeader->getName(), $nextLeaderName, $team->getMemberSlot($nextLeaderName));
            return new ServiceResult(true);

        } else {
            return new ServiceResult(false, new ServiceErrorMessage("そのような名前のプレイヤーはチームにいません"));
        }

    }
}