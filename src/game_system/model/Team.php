<?php


namespace game_system\model;


use Entity;
use ValueObject;

class Team extends Entity
{
    private $id;

    public function __construct() {
        $this->id = TeamId::asNew();
    }

    /**
     * @return TeamId
     */
    public function getId(): TeamId {
        return $this->id;
    }
}

class TeamId extends ValueObject
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