<?php


namespace mine_deep_rock\pmmp\event;


use mine_deep_rock\model\PlayerStatus;
use pocketmine\event\Event;

class PlayerLevelUpEvent extends Event
{
    /**
     * @var PlayerStatus
     */
    private $playerStatus;

    public function __construct(PlayerStatus $playerStatus) {
        $this->playerStatus = $playerStatus;
    }

    /**
     * @return PlayerStatus
     */
    public function getPlayerStatus(): PlayerStatus {
        return $this->playerStatus;
    }
}