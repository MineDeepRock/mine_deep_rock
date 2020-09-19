<?php


namespace mine_deep_rock\model;


use mine_deep_rock\pmmp\event\CoreBlockBrokeEvent;
use mine_deep_rock\pmmp\event\CoreBrokeEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use team_game_system\model\GameId;
use team_game_system\model\TeamId;

class Core
{
    /**
     * @var GameId
     */
    private $gameId;
    /**
     * @var TeamId
     */
    private $teamId;
    /**
     * @var int
     */
    private $health;
    /**
     * @var Position
     */
    private $position;


    /**
     * @var int
     */
    private $blockHealth;

    public function __construct(GameId $gameId, TeamId $teamId, Position $position, int $health = 100) {
        $this->gameId = $gameId;
        $this->teamId = $teamId;
        $this->position = $position;
        $this->health = $health;
        $this->blockHealth = 100;
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
     * @return int
     */
    public function getHealth(): int {
        return $this->health;
    }

    /**
     * @param Player $player
     * @param int $damage
     */
    public function attack(Player $player, int $damage): void {
        $this->health -= $damage;

        if ($this->health <= 0) {
            $this->health = 0;
            $event = new CoreBrokeEvent($player, $this->gameId, $this->teamId);
            $event->call();
        }
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getBlockHealth(): int {
        return $this->blockHealth;
    }

    public function attackCoreBlock(Player $player, int $damage): void {
        $this->blockHealth -= $damage;
        if ($this->blockHealth <= 0) {
            $this->blockHealth = 100;
            $event = new CoreBlockBrokeEvent($player, $this->gameId, $this->teamId);
            $event->call();
        }
    }
}