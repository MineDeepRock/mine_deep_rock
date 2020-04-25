<?php


namespace game_system\model\map;


use game_system\model\GameType;

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

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}