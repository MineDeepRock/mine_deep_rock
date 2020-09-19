<?php


namespace mine_deep_rock\pmmp\event;


use pocketmine\event\Event;
use team_game_system\model\GameId;
use team_game_system\model\TeamId;

class CoreBrokeEvent extends Event
{
    private $gameId;
    private $teamId;

    public function __construct(GameId $gameId,TeamId $teamId) {
        $this->gameId = $gameId;
        $this->teamId = $teamId;
    }

    /**
     * @return GameId
     */
    public function getGameId(): GameId {
        return $this->gameId;
    }

    /**
     * @return TeamId
     */
    public function getTeamId(): TeamId {
        return $this->teamId;
    }
}