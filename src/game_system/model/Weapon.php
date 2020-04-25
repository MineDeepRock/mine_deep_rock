<?php


namespace game_system\model;


use ValueObject;

class Weapon extends ValueObject
{
    private $id;

    private $ownerName;
    private $killCount;
    private $winCount;

    public function __construct(string $ownerName, $killCount = 0, $winCount = 0) {
        $this->id = WeaponId::asNew();
        $this->ownerName = $ownerName;
        $this->killCount = $killCount;
        $this->winCount = $winCount;
    }
}

class WeaponId extends ValueObject
{
    private $id;

    public function value(): ?string {
        return $this->id;
    }

    public static function asNew(): WeaponId {
        return new WeaponId(uniqid());
    }

    public function __construct(String $id) {
        $this->id = $id;
    }

    public function equal(?WeaponId $id): bool {
        if ($id === null)
            return false;

        return $this->id === $id->value();
    }
}