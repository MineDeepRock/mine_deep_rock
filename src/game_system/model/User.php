<?php


namespace game_system\model;


class User
{
    private $name;
    private $money;
    private $belongTeamId;
    private $lastBelongTeamId;
    private $lastJoinedGameId;

    /**
     * @return mixed
     */
    public function getBelongTeamId() {
        return $this->belongTeamId;
    }

}