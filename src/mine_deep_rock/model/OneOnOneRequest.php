<?php


namespace mine_deep_rock\model;


use team_game_system\model\Score;

class OneOnOneRequest
{
    private $ownerName;
    private $receiverName;

    /**
     * @var string
     */
    private $mapName;
    /**
     * @var Score
     */
    private $maxScore;
    /**
     * @var int
     */
    private $timeLimit;

    function __construct(string $ownerName, string $receiverName, string $mapName, ?Score $maxScore, int $timeLimit) {
        $this->ownerName = $ownerName;
        $this->receiverName = $receiverName;
        $this->mapName = $mapName;
        $this->maxScore = $maxScore;
        $this->timeLimit = $timeLimit;
    }

    static function create(string $ownerName, string $receiverName, string $mapName, ?Score $maxScore, int $timeLimit = 600): ?OneOnOneRequest {
        if ($ownerName === $receiverName) return null;

        if ($timeLimit > 600) $timeLimit = 600;
        return new OneOnOneRequest($ownerName, $receiverName, $mapName, $maxScore, $timeLimit);
    }

    /**
     * @return string
     */
    public function getOwnerName(): string {
        return $this->ownerName;
    }

    /**
     * @return string
     */
    public function getReceiverName(): string {
        return $this->receiverName;
    }

    /**
     * @return string
     */
    public function getMapName(): string {
        return $this->mapName;
    }

    /**
     * @return null|Score
     */
    public function getMaxScore(): ?Score {
        return $this->maxScore;
    }

    /**
     * @return int
     */
    public function getTimeLimit(): int {
        return $this->timeLimit;
    }
}