<?php

namespace main_team_system\models;

class Player
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toJson()
    {
        return array(
            "name" => $this->name,
        );
    }
}