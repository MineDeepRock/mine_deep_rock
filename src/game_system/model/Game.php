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

class GameId extends ValueObject
{
    private $id;

    public function value(): ?string {
        return $this->id;
    }

    public static function asNew(): GameId {
        return new GameId(uniqid());
    }

    public function __construct(String $id) {
        $this->id = $id;
    }

    public function equal(?GameId $id): bool {
        if ($id === null)
            return false;

        return $this->id === $id->value();
    }
}