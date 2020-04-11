<?php


namespace team_system;

use Client;
use team_system\service\MemberService;
use team_system\service\TeamService;

class TeamSystemClient extends Client
{
    private $teamService;
    private $memberService;

    public function __construct(TeamService $teamService, MemberService $memberService) {
        $this->teamService = $teamService;
        $this->memberService = $memberService;
    }

    public function create(string $memberName, $whenSucceed): void {
        $member = $this->memberService->getData($memberName);

        $result = $this->teamService->create($member);
        if ($result->isSucceed()) {
            $this->memberService->updateBelongTeamId($member, $result->getValue()->getId());
            $message = "チームを作成しました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }

    public function join(string $memberName, string $leaderName,$whenSucceed): void {
        $member = $this->memberService->getData($memberName);

        $result = $this->teamService->join($member, $leaderName);
        if ($result->isSucceed()) {
            $this->memberService->updateBelongTeamId($member, $result->getValue()->getId());
            $message = "チームに参加しました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }

    public function quit(string $memberName, $whenSucceed): void {
        $member = $this->memberService->getData($memberName);

        $result = $this->teamService->quit($member, $member->getBelongTeamId());
        if ($result->isSucceed()) {
            $this->memberService->updateBelongTeamId($member, null);
            $message = "チームを抜けました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }

    public function yield(string $memberName, $whenSucceed,string $nextLeader = null) {
        $member = $this->memberService->getData($memberName);

        $result = $this->teamService->yieldLeader($member, $nextLeader);

        if ($nextLeader == null) {
            $this->memberService->updateBelongTeamId($member, null);
            $message = "譲る相手がいなかったので、チームを削除しました";
        } else if ($result->isSucceed()) {
            $message = "チームのオーナーを{$nextLeader}に譲りました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }
}