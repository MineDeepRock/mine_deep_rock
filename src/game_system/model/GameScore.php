<?php


namespace game_system\model;


class GameScore
{
    private $gameId;
    private $teamId;

    private $name;
    private $killCount;
    private $point;

    public function __construct(GameId $gameId, TeamId $teamId, string $name, int $killCount = 0, int $point = 0) {
        $this->gameId = $gameId;
        $this->teamId = $teamId;
        $this->name = $name;
        $this->killCount = $killCount;
        $this->point = $point;
    }

    public static function fromJson(array $json): GameScore {
        $gameId = new GameId($json["game_id"]);
        $teamId = new TeamId($json["team_id"]);
        $name = $json["name"];
        $killCount = intval($json["kill_count"]);
        $point = intval($json["point"]);

        return new GameScore($gameId, $teamId, $name, $killCount, $point);
    }

    public function toString(): string {
        return "" . $this->name . "  " . $this->killCount . " " . $this->point;
    }

    /**
     * @return TeamId
     */
    public function getTeamId(): TeamId {
        return $this->teamId;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPoint(): int {
        return $this->point;
    }
}