<?php


namespace team_system;

use Client;
use team_system\services\MemberService;
use team_system\services\TeamService;

class TeamSystemClient extends Client
{
    private $teamService;
    private $memberService;
    private $notifier;

    public function __construct(TeamService $teamService, MemberService $memberService,TeamSystemNotifier $notifier) {
        $this->teamService = $teamService;
        $this->memberService = $memberService;
        $this->notifier = $notifier;
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

        $result = $this->teamService->quit($member);
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

    public function onJoin(string $playerName){
        $this->memberService->init($playerName);
    }

    public function onLeave(string $playerName){
        $member = $this->memberService->getData($playerName);
        $this->teamService->quit($member);
    }
}