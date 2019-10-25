<?php

namespace team_system\models;

class Team
{

    private $id;
    private $owner;
    private $members;

    public function join(){
        //TODO:参加
    }

    public function __construct(Player $owner)
    {
        $this->owner = $owner;
    }
}

/*
 * id
 * owner
 * members
 */