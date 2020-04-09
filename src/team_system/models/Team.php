<?php

namespace team_system\models;

class Team
{
    private $id;
    private $owner;

    private $first_coworker_name;
    private $second_coworker_name;
    private $third_coworker_name;


    /**
     * @return array
     */
    public function getCoworkersName(): array {
        $coworkers = [];
        if ($this->first_coworker_name !== null) array_push($coworkers, $this->first_coworker_name);
        if ($this->second_coworker_name !== null) array_push($coworkers, $this->second_coworker_name);
        if ($this->third_coworker_name !== null) array_push($coworkers, $this->third_coworker_name);

        return $coworkers;
    }

    /**
     * @return bool
     */
    public function isFull(): bool {
        return count($this->getCoworkersName()) === 3;
    }


    /**
     * @return bool
     */
    public function isEmpty(): bool {
        return count($this->getCoworkersName()) === 0;
    }

    /**
     * @param String $coworkerName
     * @return void
     */
    public function setToEmptySlot(String $coworkerName): void {
        $emptySlot = $this->nextEmptySlot();
        if ($emptySlot === "first_coworker_name") {
            $this->first_coworker_name = $coworkerName;

        } else if ($emptySlot === "second_coworker_name") {
            $this->second_coworker_name = $coworkerName;

        } else {
            $this->third_coworker_name = $coworkerName;
        }
    }


    /**
     * @return string
     */
    public function nextEmptySlot(): string {
        if ($this->first_coworker_name === null) {
            return "first_coworker_name";

        } else if ($this->second_coworker_name === null) {
            return "second_coworker_name";

        } else {
            return "third_coworker_name";
        }
    }

    /**
     * @param String $playerName
     * @return string
     */
    //TODO:リネーム
    public function isWherePlayerSlot(String $playerName): string {
        if ($playerName === $this->first_coworker_name) {
            return "first_coworker_name";

        } else if ($playerName === $this->second_coworker_name) {
            return "second_coworker_name";

        } else {
            return "third_coworker_name";
        }
    }

    /**
     * Team constructor.
     * @param TeamId $id
     * @param Player $owner
     * @param String|null $first_coworker_name
     * @param String|null $second_coworker_name
     * @param String|null $third_coworker_name
     */
    public function __construct(TeamId $id, Player $owner, String $first_coworker_name = null, String $second_coworker_name = null, String $third_coworker_name = null) {
        $this->id = $id;
        $this->owner = $owner;
        $this->first_coworker_name = $first_coworker_name;
        $this->second_coworker_name = $second_coworker_name;
        $this->third_coworker_name = $third_coworker_name;
    }

    /**
     * @param Player $owner
     * @return Team
     */
    public static function asNew(Player $owner) {
        $id = new TeamId();
        return new Team($id, $owner);
    }

    /**
     * @param array $json
     * @return Team
     */
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
}


class TeamId
{
    private $id;

    /**
     * @return string|null
     */
    public function value(): ?string {
        return $this->id;
    }

    /**
     * TeamId constructor.
     * @param String|null $id
     */
    public function __construct(?String $id = "init") {
        $this->id = $id === "init" ? uniqid() : $id;
    }

}