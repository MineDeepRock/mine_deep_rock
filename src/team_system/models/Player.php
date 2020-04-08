<?php

namespace team_system\models;

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
     * @param TeamId|null $belongTeamId
     */
    public function setBelongTeamId(?TeamId $belongTeamId): void {
        $this->belongTeamId = $belongTeamId;
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
            "belong_Team_id" => $this->belongTeamId,
        );
    }

    public static function fromJson(array $json):Player {
        $name = $json["name"];
        $belongTeamId = $json["belong_team_id"];

        $player = new Player($name);
        $player->setBelongTeamId(new TeamId($belongTeamId));

        return $player;
    }
}
