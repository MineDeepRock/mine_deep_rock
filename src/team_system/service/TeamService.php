<?php

namespace team_system\service;

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
        return $this->repository->contain($owner);
    }

    /**
     * @param Player $owner
     */
    public function create(Player $owner): void {

        if ($this->contain($owner)) {
            //TODO
        } else {
            $createdTeam = Team::asNew($owner);

            $this->repository->create($createdTeam);
        }
    }

    /**
     * @param Player $sender
     * @param String $ownerName
     */
    public function join(Player $sender, String $ownerName): void {

        $team = $this->repository->search($ownerName);

        if ($sender->getBelongTeamId() != null) {
            //TODO
        } else if ($team == null) {
            //TODO
        } else if ($team->isFull()) {
            //TODO
        } else {
            $this->repository->join($sender, $team);
            $sender->setBelongTeamId($team->getId());
        }
    }

    /**
     * @param Player $sender
     * @param String $ownerName
     */
    public function quit(Player $sender, String $ownerName): void {

        $team = $this->repository->search($ownerName);

        if ($sender->getBelongTeamId() == null) {
            //TODO
        } else if ($team == null) {
            //TODO
        } else {
            $this->repository->quit($sender, $team);
            $sender->setBelongTeamId(null);
        }
    }

    public function breakup() {
        // TODO: Implement breakup() method.
    }
}