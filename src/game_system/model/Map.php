<?php


namespace game_system\model;


abstract class Map
{
    private $name;
    private $creatorName;
    private $supportGameType;

    public function __construct(string $name, string $creatorName, GameType $supportGameType) {

        $this->name = $name;
        $this->creatorName = $creatorName;
        $this->supportGameType = $supportGameType;
    }
}