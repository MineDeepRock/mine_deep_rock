<?php


namespace game_system\model;


class Grenade
{
    private $name;
    private $range;
    private $delay;
    private $duration;

    public function __construct(string $name, int $range, int $delay, $duration = 0) {
        $this->name = $name;
        $this->range = $range;
        $this->delay = $delay;
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDelay(): int {
        return $this->delay;
    }

    /**
     * @return int
     */
    public function getDuration(): int {
        return $this->duration;
    }

    /**
     * @return int
     */
    public function getRange(): int {
        return $this->range;
    }

}