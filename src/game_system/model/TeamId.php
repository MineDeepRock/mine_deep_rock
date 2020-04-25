<?php


namespace game_system\model;

class TeamId
{
    private $id;

    public function value(): ?string {
        return $this->id;
    }

    public static function asNew(): TeamId {
        return new TeamId(uniqid());
    }

    public function __construct(String $id) {
        $this->id = $id;
    }

    public function equal(?TeamId $id): bool {
        if ($id === null)
            return false;

        return $this->id === $id->value();
    }
}