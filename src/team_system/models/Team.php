<?php

namespace team_system\models;

class Team
{
    private $id;
    private $owner;

    private $first_coworker_name;
    private $second_coworker_name;
    private $third_coworker_name;

    public function isFull(): bool {
        return $this->first_coworker_name != null && $this->second_coworker_name != null && $this->third_coworker_name != null;
    }

    public function setToEmptySlot(String $coworkerName): string {
        if ($this->first_coworker_name == null) {
            $this->first_coworker_name = $coworkerName;
            return "first_coworker_name";

        } else if ($this->second_coworker_name == null) {
            $this->second_coworker_name = $coworkerName;
            return "second_coworker_name";

        } else {
            $this->third_coworker_name = $coworkerName;
            return "third_coworker_name";
        }
    }

    public function __construct(TeamId $id, Player $owner, String $first_coworker_name = null, String $second_coworker_name = null, String $third_coworker_name = null) {
        $this->id = $id;
        $this->owner = $owner;
        $this->first_coworker_name = $first_coworker_name;
        $this->second_coworker_name = $second_coworker_name;
        $this->third_coworker_name = $third_coworker_name;
    }

    public static function asNew(Player $owner) {
        $id = new TeamId();
        return new Team($id, $owner);
    }

    public static function fromJson(array $json): Team {
        $id = new TeamId($id = $json["id"]);
        $owner = new Player($json["owner_name"]);
        $first = $json["first_coworker_name"];
        $second = $json["second_coworker_name"];
        $third = $json["third_coworker_name"];

        return new Team($id, $owner, $first, $second, $third);
    }

    /**
     * @return TeamId
     */
    public function getId(): TeamId {
        return $this->id;
    }

    /**
     * @return Player
     */
    public function getOwner(): Player {
        return $this->owner;
    }

    /**
     * @return array
     */
    public function getCoworkerNames(): array {
        return array($this->first_coworker_name, $this->second_coworker_name, $this->third_coworker_name);
    }
}


class TeamId
{
    private $id;

    /**
     * @return mixed
     */
    public function value() {
        return $this->id;
    }

    static function fromString(String $id) {
        return new TeamId();
    }

    public function __construct(String $id = null) {
        $this->id = $id ?? uniqid();
    }

}