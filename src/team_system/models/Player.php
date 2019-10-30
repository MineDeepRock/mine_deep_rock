<?php

namespace team_system\models;

class Player
{
    private $name;

    public function getName(): string {
        return $this->name;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toJson(): array
    {
        return array(
            "name" => $this->name,
        );
    }
}
