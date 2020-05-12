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

    private $isRedTeam = false;
    private $isBlueTeam = false;

    private $onOccupied;

    public function __construct(string $name, Coordinate $coordinate) {
        $this->center = $coordinate;
        $this->name = $name;
    }

    public function makeProgressByRed(): bool {
        if ($this->gauge === 100) return false;

        $this->gauge += 5;
        if ($this->gauge === 0) {
            $this->isBlueTeam = false;
            $this->isRedTeam = false;
        }
        if ($this->gauge === 100) {
            $this->isBlueTeam = false;
            $this->isRedTeam = true;
            ($this->onOccupied)($this->name, $this->gauge);
        }
        return true;
    }

    public function makeProgressByBlue(): bool {
        if ($this->gauge === -100) return false;

        $this->gauge -= 5;
        if ($this->gauge === 0) {
            $this->isBlueTeam = false;
            $this->isRedTeam = false;
        }
        if ($this->gauge === -100) {
            $this->isBlueTeam = true;
            $this->isRedTeam = false;
            ($this->onOccupied)($this->name, $this->gauge);
        }
        return true;
    }

    public function isOccupied(): bool {
        return $this->isBlueTeams() || $this->isRedTeams();
    }

    public function isRedTeams(): bool {
        return $this->isRedTeam;
    }

    public function isBlueTeams(): bool {
        return $this->isBlueTeam;
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

    /**
     * @return int
     */
    public function getGauge(): int {
        return $this->gauge;
    }
}