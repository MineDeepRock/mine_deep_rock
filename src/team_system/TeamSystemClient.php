<?php


namespace team_system;

use Client;
use team_system\service\PlayerService;
use team_system\service\TeamService;

class TeamSystemClient extends Client
{
    private $teamService;
    private $playerService;

    public function __construct(TeamService $teamService, PlayerService $playerService) {
        $this->teamService = $teamService;
        $this->playerService = $playerService;
    }

    public function create(string $playerName, $whenSucceed): void {
        $player = $this->playerService->getData($playerName);

        $result = $this->teamService->create($player);
        if ($result->isSucceed()) {
            $this->playerService->updateBelongTeamId($player, $result->getValue()->getId());
            $message = "チームを作成しました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }

    public function join(string $playerName, string $ownerName,$whenSucceed): void {
        $player = $this->playerService->getData($playerName);

        $result = $this->teamService->join($player, $ownerName);
        if ($result->isSucceed()) {
            $this->playerService->updateBelongTeamId($player, $result->getValue()->getId());
            $message = "チームに参加しました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }

    public function quit(string $playerName, $whenSucceed): void {
        $player = $this->playerService->getData($playerName);

        $result = $this->teamService->quit($player, $player->getBelongTeamId());
        if ($result->isSucceed()) {
            $this->playerService->updateBelongTeamId($player, null);
            $message = "チームを抜けました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }

    public function yield(string $playerName, $whenSucceed,string $nextOwner = null) {
        $player = $this->playerService->getData($playerName);

        $result = $this->teamService->yieldOwner($player, $nextOwner);

        if ($nextOwner == null) {
            $this->playerService->updateBelongTeamId($player, null);
            $message = "譲る相手がいなかったので、チームを削除しました";
        } else if ($result->isSucceed()) {
            $message = "チームのオーナーを{$nextOwner}に譲りました";
        } else {
            $message = $result->getValue()->getMessage();
        }
        $whenSucceed($message);
    }
}