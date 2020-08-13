<?php


namespace mine_deep_rock\model;


class PlayerGameStatus
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $isResuscitated;
    /**
     * @var int
     */
    private $killCountInGame;

    public function __construct(string $name, bool $isResuscitated, int $killCountInGame) {
        $this->name = $name;
        $this->isResuscitated = $isResuscitated;
        $this->killCountInGame = $killCountInGame;
    }

    static function asNew(string $name): PlayerGameStatus {
        return new PlayerGameStatus($name, false, 0);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isResuscitated(): bool {
        return $this->isResuscitated;
    }

    /**
     * @return int
     */
    public function getKillCountInGame(): int {
        return $this->killCountInGame;
    }
}