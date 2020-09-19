<?php


namespace mine_deep_rock\pmmp\event;


use pocketmine\event\Event;
use pocketmine\Player;
use team_game_system\model\GameId;
use team_game_system\model\TeamId;

class CoreBlockBrokeEvent extends Event
{
    private $player;
    private $gameId;
    private $teamId;

    public function __construct(Player $player, GameId $gameId, TeamId $teamId) {
        $this->gameId = $gameId;
        $this->teamId = $teamId;
        $this->player = $player;
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

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }
}