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

    public function __construct(int $rank, int $totalXp) {
        $this->rank = $rank;
        $this->totalXp = $totalXp;
    }

    static function asNew(): PlayerLevel {
        return new PlayerLevel(1, 0);
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
}