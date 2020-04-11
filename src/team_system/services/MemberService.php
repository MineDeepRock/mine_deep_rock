<?php


namespace team_system\services;


use Service;
use team_system\models\Member;
use team_system\models\TeamId;
use team_system\repositories\MemberRepository;

class MemberService extends Service
{
    private $repository;

    public function __construct() {
        $this->repository = new MemberRepository();
    }

    public function init(string $memberName):Member {
        $initializedMember = new Member($memberName);
        $this->repository->init($initializedMember);
        return $initializedMember;
    }

    public function getData(string $name): Member {
        return $this->repository->getData($name);
    }

    /**
     * @param Member $member
     * @param TeamId|null $teamId
     * @return Member
     */
    public function updateBelongTeamId(Member $member, ?TeamId $teamId): Member {
        $this->repository->updateBelongTeamId($member->getName(), $teamId);
        return $this->getData($member->getName());
    }
}