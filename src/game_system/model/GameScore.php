<?php


namespace game_system\model;


class GameScore
{
    private $gameId;

    private $name;
    private $killCount;
    private $point;

    public function __construct(GameId $gameId, string $name, int $killCount = 0, int $point = 0) {
        $this->gameId = $gameId;
        $this->name = $name;
        $this->killCount = $killCount;
        $this->point = $point;
    }

    public static function fromJson(array $json): GameScore {
        $gameId = new GameId($json["game_id"]);
        $name = $json["name"];
        $killCount = intval($json["kill_count"]);
        $point = intval($json["point"]);

        return new GameScore($gameId, $name, $killCount, $point);
    }

    public function toString() :string {
        return "name:" . $this->name . " kill:" . $this->killCount . " point:" . $this->point;
    }
}