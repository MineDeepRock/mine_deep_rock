<?php


namespace game_system\model;


class User
{
    private $name;
    private $winCount;
    private $money;
    private $belongTeamId;
    private $participatedGameId;
    private $lastBelongTeamId;

    private $selectedWeaponName;

    public function __construct(string $name, int $winCount, int $money, TeamId $belongTeamId, TeamId $lastBelongTeamId, GameId $participatedGameId,string $selectedWeaponName = "M1907SL") {
        $this->name = $name;
        $this->winCount = $winCount;
        $this->money = $money;
        $this->belongTeamId = $belongTeamId;
        $this->participatedGameId = $participatedGameId;
        $this->lastBelongTeamId = $lastBelongTeamId;
        $this->selectedWeaponName = $selectedWeaponName;
    }

    public static function fromJson(array $json): User {
        $name = $json["name"];
        $winCount = intval($json["win_count"]);
        $money = intval($json["money"]);
        $belongTeamId = new TeamId($json["belong_team_id"]);
        $lastBelongTeaId = new TeamId($json["last_belong_team_id"]);
        $participatedGameId = new GameId($json["participated_game_id"]);

        return new User($name, $winCount, $money, $belongTeamId, $lastBelongTeaId, $participatedGameId);
    }

    /**
     * @return mixed
     */
    public function getBelongTeamId() {
        return $this->belongTeamId;
    }

    /**
     * @return mixed
     */
    public function getParticipatedGameId() {
        return $this->participatedGameId;
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

    /**
     * @return string
     */
    public function getSelectedWeaponName(): string {
        return $this->selectedWeaponName;
    }

}