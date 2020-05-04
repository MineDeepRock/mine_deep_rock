<?php


namespace game_system\model;


abstract class Box
{
    const NAME = "";
    private $secondLimit;
    protected $playerUsed = [];

    public function __construct(int $secondLimit) {
        $this->secondLimit = $secondLimit;
    }

    /**
     * @return int
     */
    public function getSecondLimit(): int {
        return $this->secondLimit;
    }

    /**
     * @return array
     */
    public function getPlayerUsed(): array {
        return $this->playerUsed;
    }
}