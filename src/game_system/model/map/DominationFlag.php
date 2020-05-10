<?php


namespace game_system\model\map;


use game_system\model\Coordinate;
use pocketmine\utils\TextFormat;

class DominationFlag
{
    private $center;
    private $gauge = 0;
    private $name;

    public function __construct(string $name, Coordinate $coordinate) {
        $this->center = $coordinate;
        $this->name = $name;
    }

    public function makeProgressByRed(): int {
        $this->gauge += 5;
        return $this->gauge;
    }

    public function makeProgressByBlue(): int {
        $this->gauge -= 5;
        return $this->gauge;
    }

    public function isOccupied(): bool {
        return $this->isBlueTeams() || $this->isRedTeams();
    }

    public function isBlueTeams(): bool {
        return $this->gauge === -100;
    }

    public function isRedTeams(): bool {
        return $this->gauge === 100;
    }

    /**
     * @return Coordinate
     */
    public function getCenter(): Coordinate {
        return $this->center;
    }

    public function toString(): string {
        if ($this->gauge === 0) return TextFormat::WHITE . $this->name . ":0/100";
        if ($this->gauge > 0) {
            return TextFormat::RED . $this->name . ":" . $this->gauge . TextFormat::WHITE . "/100";
        } else {
            return TextFormat::BLUE . $this->name . ":" . -$this->gauge . TextFormat::WHITE . "/100";
        }
    }
}