<?php


namespace game_system\model;


class User
{
    private $name;
    private $money;
    private $belongTeamId;
    private $belongGameId;

    private $lastBelongTeamId;

    /**
     * @return mixed
     */
    public function getBelongTeamId() {
        return $this->belongTeamId;
    }

    /**
     * @return mixed
     */
    public function getBelongGameId() {
        return $this->belongGameId;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLastBelongTeamId() {
        return $this->lastBelongTeamId;
    }

    public function resetBelongTeamId(): void {
        $this->belongTeamId = null;
    }

}