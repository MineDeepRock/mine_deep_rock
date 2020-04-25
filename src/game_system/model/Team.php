<?php


namespace game_system\model;


use ValueObject;

class Team extends ValueObject
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