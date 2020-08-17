<?php


namespace mine_deep_rock\model;


use LogicException;
use team_game_system\model\TeamId;

class DominationFlagGauge
{
    /**
     * @var TeamId[]
     */
    private $gauge;

    /**
     * @var null|TeamId
     */
    private $owingTeamId;

    /**
     * @var null|TeamId
     */
    private $occupyingTeamId;

    public function __construct(array $gauge, ?TeamId $owingTeam, ?TeamId $occupyingTeam) {
        $this->gauge = $gauge;
        $this->owingTeamId = $owingTeam;
        $this->occupyingTeamId = $occupyingTeam;
    }

    static function asNew(): DominationFlagGauge {

        return new DominationFlagGauge([], null, null);
    }

    public function add(TeamId $teamId, int $value): void {
        if ($value < 0) {
            throw new LogicException("value < 0");
        }

        $add = array_fill(0, $value - 1, $teamId);
        $this->gauge = array_merge($this->gauge, $add);
        if (count($this->gauge) > 100) {
            $this->gauge = array_slice($this->gauge, 0, 99);
        }


        //$valueが0でも大丈夫なように$teamIdはつかわない
        if (count($this->gauge) === 100) {
            $this->occupyingTeamId = $this->gauge[count($this->gauge) - 1];
        }
        if (count($this->gauge) > 1) {
            $this->owingTeamId = $this->gauge[count($this->gauge) - 1];
        }
    }

    public function reduce(TeamId $teamId, int $value): void {
        if ($value < 0) {
            throw new LogicException("value < 0");
        }

        $remainder = count($this->gauge) - $value;

        //先にやる
        if ($remainder <= 0) {
            $this->occupyingTeamId = null;
            $this->owingTeamId = null;
        }

        if ($remainder === 0) {
            $this->gauge = [];

        }
        if ($remainder < 0) {
            $this->gauge = [];
            $this->add($teamId, $remainder);

        } else {
            $this->gauge = array_slice($this->gauge, 0, $value - 1);

        }
    }

    public function isEmpty(): bool {
        return $this->owingTeamId === null;
    }

    public function isOccupied(): bool {
        return $this->occupyingTeamId !== null;
    }

    //TODO:正しい英語？
    public function isOwned(): bool {
        return $this->owingTeamId !== null;
    }

    /**
     * @return TeamId|null
     */
    public function getOwingTeamId(): ?TeamId {
        return $this->owingTeamId;
    }

    /**
     * @return TeamId|null
     */
    public function getOccupyingTeamId(): ?TeamId {
        return $this->occupyingTeamId;
    }

    public function asInt(): int {
        return count($this->gauge);
    }
}