<?php


namespace game_system\model;


use ValueObject;

abstract class Game extends ValueObject
{
    private $id;
    protected $isStarted;

    public function __construct() {
        $this->id = GameId::asNew();
        $this->isStarted = false;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool {
        return $this->isStarted;
    }

}

class GameType
{
    private $type;

    public function __construct(string $type) {
        $this->type = $type;
    }

    public function equal(GameType $gunType): bool {
        return $this->type == $gunType->type;
    }

    public static function TeamDeathMatch(): GameType {
        return new GameType("TeamDeathMatch");
    }

    /**
     * @return string
     */
    public function getTypeText() {
        return $this->type;
    }
}