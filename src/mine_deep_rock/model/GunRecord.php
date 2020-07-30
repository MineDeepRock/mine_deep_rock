<?php


namespace mine_deep_rock\model;


class GunRecord
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $killCount;
    /**
     * @var int
     */
    private $scopeMagnification;


    public function __construct(string $name, int $killCount, int $scopeMagnification) {
        $this->name = $name;
        $this->killCount = $killCount;
        $this->scopeMagnification = $scopeMagnification;
    }

    static function asNew(string $name): GunRecord {
        return new GunRecord($name, 0, 1);
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
    public function getKillCount(): int {
        return $this->killCount;
    }

    /**
     * @return int
     */
    public function getScopeMagnification(): int {
        return $this->scopeMagnification;
    }
}