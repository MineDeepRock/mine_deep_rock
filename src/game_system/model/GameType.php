<?php


namespace game_system\model;


class GameType
{
    private $type;

    public function __construct(string $type) {
        $this->type = $type;
    }

    public function equal(GameType $gunType): bool {
        return $this->type == $gunType->type;
    }

    public static function TeamDeathMatch(): GameType {
        return new GameType("TeamDeathMatch");
    }

    public static function TeamDomination(): GameType {
        return new GameType("TeamDomination");
    }

    /**
     * @return string
     */
    public function getTypeText() {
        return $this->type;
    }
}