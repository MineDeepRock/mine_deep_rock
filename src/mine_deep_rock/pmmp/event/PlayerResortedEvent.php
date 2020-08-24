<?php


namespace mine_deep_rock\pmmp\event;


use pocketmine\event\Event;
use pocketmine\Player;

class PlayerResortedEvent extends Event
{

    /**
     * @var Player
     */
    private $player;
    /**
     * @var bool
     */
    private $byRescue;

    public function __construct(Player $player, bool $byRescue) {
        $this->player = $player;
        $this->byRescue = $byRescue;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return bool
     */
    public function isByRescue(): bool {
        return $this->byRescue;
    }
}