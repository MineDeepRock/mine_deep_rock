<?php


namespace mine_deep_rock\model;


class PlayerLevel
{
    /**
     * @var int
     */
    private $rank;
    /**
     * @var int
     */
    private $totalXp;
    private $xpToNextLevel;

    public function __construct(int $rank, int $totalXp, int $xpToNextLevel) {
        $this->rank = $rank;
        $this->totalXp = $totalXp;
        $this->xpToNextLevel = $xpToNextLevel;
    }

    static function asNew(): PlayerLevel {
        return new PlayerLevel(1, 0, 500);
    }

    /**
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
    }

    /**
     * @return int
     */
    public function getTotalXp(): int {
        return $this->totalXp;
    }

    /**
     * @return int
     */
    public function getXpToNextLevel(): int {
        return $this->xpToNextLevel;
    }
}