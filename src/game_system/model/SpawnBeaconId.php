<?php


namespace game_system\model;


class SpawnBeaconId
{
    private $id;

    public function value(): ?string {
        return $this->id;
    }

    public static function asNew(): SpawnBeaconId {
        return new SpawnBeaconId(uniqid());
    }

    public function __construct(String $id) {
        $this->id = $id;
    }

    public function equal(?SpawnBeaconId $id): bool {
        if ($id === null)
            return false;

        return $this->id === $id->value();
    }
}