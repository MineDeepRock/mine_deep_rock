<?php


namespace game_system\model;


use game_system\model\military_department\MilitaryDepartment;

class User
{
    private $name;
    private $winCount;
    private $money;
    private $militaryDepartment;

    private $belongTeamId;
    private $participatedGameId;
    private $lastBelongTeamId;

    private $selectedWeaponName;
    private $selectedSubWeaponName;

    public function __construct(string $name, int $winCount, int $money, MilitaryDepartment $militaryDepartment, ?TeamId $belongTeamId, ?TeamId $lastBelongTeamId, ?GameId $participatedGameId, string $selectedWeaponName = "M1907SL", string $selectedSubWeaponName = "Mle1903") {
        $this->name = $name;
        $this->winCount = $winCount;
        $this->money = $money;
        $this->militaryDepartment = $militaryDepartment;
        $this->belongTeamId = $belongTeamId;
        $this->participatedGameId = $participatedGameId;
        $this->lastBelongTeamId = $lastBelongTeamId;
        $this->selectedWeaponName = $selectedWeaponName;
        $this->selectedSubWeaponName = $selectedSubWeaponName;
    }

    public static function fromJson(array $json): User {
        $name = $json["name"];
        $winCount = intval($json["win_count"]);
        $money = intval($json["money"]);
        $militaryDepartment = MilitaryDepartment::fromName($json["military_department"]);
        $belongTeamId = $json["belong_team_id"] === null ? null : new TeamId($json["belong_team_id"]);
        $lastBelongTeaId = $json["last_belong_team_id"] === null ? null : new TeamId($json["last_belong_team_id"]);
        $participatedGameId = $json["participated_game_id"] === null ? null : new GameId($json["participated_game_id"]);
        $selectedWeaponName = $json["selected_weapon"];
        $selectedSubWeaponName = $json["selected_sub_weapon"];

        return new User($name, $winCount, $money, $militaryDepartment, $belongTeamId, $lastBelongTeaId, $participatedGameId, $selectedWeaponName, $selectedSubWeaponName);
    }

    /**
     * @return TeamId|null
     */
    public function getBelongTeamId(): ?TeamId {
        return $this->belongTeamId;
    }

    /**
     * @return GameId|null
     */
    public function getParticipatedGameId(): ?GameId {
        return $this->participatedGameId;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return TeamId|null
     */
    public function getLastBelongTeamId(): ?TeamId {
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

    /**
     * @return string
     */
    public function getSelectedSubWeaponName(): string {
        return $this->selectedSubWeaponName;
    }

    /**
     * @return int
     */
    public function getMoney(): int {
        return $this->money;
    }

    /**
     * @return MilitaryDepartment
     */
    public function getMilitaryDepartment(): MilitaryDepartment {
        return $this->militaryDepartment;
    }

}