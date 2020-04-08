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
     * @return TeamId
     */
    public function getBelongTeamId(): ?TeamId {
        return $this->belongTeamId;
    }

    /**
     * @param TeamId $belongTeamId
     */
    public function setBelongTeamId(TeamId $belongTeamId): void {
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
        );
    }
}
