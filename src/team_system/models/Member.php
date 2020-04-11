<?php

namespace team_system\models;

use Entity;

require "Team.php";

class Member extends Entity
{
    private $name;
    private $belongTeamId;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return TeamId|null
     */
    public function getBelongTeamId(): ?TeamId {
        return $this->belongTeamId;
    }

    /**
     * Player constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function toJson(): array {
        return array(
            "name" => $this->name,
            "belong_team_id" => $this->belongTeamId,
        );
    }

    public static function fromJson(array $json): Member {
        $name = $json["name"];
        $belongTeamId = $json["belong_team_id"];

        $member = new Member($name);
        $member->belongTeamId = $belongTeamId == null ? null : new TeamId($belongTeamId);

        return $member;
    }
}
