<?php

namespace team_system\models;

require "Team.php";

class Player
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

    public static function fromJson(array $json): Player {
        $name = $json["name"];
        $belongTeamId = $json["belong_team_id"];

        $player = new Player($name);
        $player->belongTeamId = $belongTeamId == null ? null : new TeamId($belongTeamId);

        return $player;
    }
}
