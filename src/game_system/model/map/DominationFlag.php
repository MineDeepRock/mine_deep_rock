<?php


namespace game_system\model\map;


use Closure;
use game_system\model\Coordinate;
use pocketmine\utils\TextFormat;

class DominationFlag
{
    private $center;
    private $gauge = 0;
    private $name;

    private $onOccupied;

    public function __construct(string $name, Coordinate $coordinate) {
        $this->center = $coordinate;
        $this->name = $name;
    }

    public function makeProgressByRed(): int {
        $this->gauge += 5;
        if ($this->gauge === 100) ($this->onOccupied)($this->name, $this->gauge);
        return $this->gauge;
    }

    public function makeProgressByBlue(): int {
        $this->gauge -= 5;
        if ($this->gauge === -100) ($this->onOccupied)($this->name, $this->gauge);
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
        $nameText = TextFormat::WHITE . $this->name;
        $gaugeText = TextFormat::WHITE . "0/100";

        if ($this->gauge > 0) {
            $gaugeText = TextFormat::RED . $this->gauge . TextFormat::WHITE . "/100";
        } else if ($this->gauge < 0) {
            $gaugeText = TextFormat::BLUE . -$this->gauge . TextFormat::WHITE . "/100";
        }

        if ($this->isRedTeams()) {
            $nameText = TextFormat::RED . $this->name;
        } else if ($this->isBlueTeams()) {
            $nameText = TextFormat::BLUE . $this->name;
        }

        return $nameText . ":" . $gaugeText;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param mixed $onOccupied
     * @return DominationFlag
     */
    public function setOnOccupied(Closure $onOccupied) {
        $this->onOccupied = $onOccupied;
        return $this;
    }
}