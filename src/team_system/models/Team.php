<?php

namespace team_system\models;

class Team
{
    private $id;
    private $owner;
    private $members;

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return Player
     */
    public function getOwner(): Player {
        return $this->owner;
    }

    public function join() {
        //TODO:参加
    }

    public function __construct(Player $owner) {
        $this->id = uniqid();
        $this->owner = $owner;
    }
}

/*
 * id
 * owner
 * members
 */
