<?php


namespace game_system\model;


class GameId
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