<?php

namespace team_system\models;

use ValueObject;

class Team extends ValueObject
{
    private $id;
    private $leaderName;

    private $firstMemberName;
    private $secondMemberName;
    private $thirdMemberName;

    /**
     * @return array
     */
    public function getCoworkersName(): array {
        $members = [];
        if ($this->firstMemberName !== null) array_push($members, $this->firstMemberName);
        if ($this->secondMemberName !== null) array_push($members, $this->secondMemberName);
        if ($this->thirdMemberName !== null) array_push($members, $this->thirdMemberName);

        return $members;
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
     * @return string
     */
    public function nextEmptySlot(): string {
        if ($this->firstMemberName === null) {
            return "first_member_name";

        } else if ($this->secondMemberName === null) {
            return "second_member_name";

        } else {
            return "third_member_name";
        }
    }

    /**
     * @param string $memberName
     * @return string
     */
    //TODO:リネーム
    public function getMemberSlot(string $memberName): string {
        if ($memberName === $this->firstMemberName) {
            return "first_member_name";

        } else if ($memberName === $this->secondMemberName) {
            return "second_member_name";

        } else {
            return "third_member_name";
        }
    }

    /**
     * Team constructor.
     * @param TeamId $id
     * @param string $leaderName
     * @param String|null $firstMemberName
     * @param String|null $secondMemberName
     * @param String|null $thirdMemberName
     */
    public function __construct(TeamId $id, string $leaderName, String $firstMemberName = null, String $secondMemberName = null, String $thirdMemberName = null) {
        $this->id = $id;
        $this->leaderName = $leaderName;
        $this->firstMemberName = $firstMemberName;
        $this->secondMemberName = $secondMemberName;
        $this->thirdMemberName = $thirdMemberName;
    }


    public static function asNew(string $leaderName) {
        $id = TeamId::asNew();
        return new Team($id, $leaderName);
    }

    /**
     * @param array $json
     * @return Team
     */
    public static function fromJson(array $json): Team {
        $id = new TeamId($json["id"]);
        $leader = $json["leader_name"];
        $first = $json["first_member_name"];
        $second = $json["second_member_name"];
        $third = $json["third_member_name"];

        return new Team($id, $leader, $first, $second, $third);
    }

    /**
     * @return TeamId
     */
    public function getId(): TeamId {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLeaderName(): string {
        return $this->leaderName;
    }
}


class TeamId
{
    private $id;

    public function value(): ?string {
        return $this->id;
    }

    public static function asNew(): TeamId {
        return new TeamId(uniqid());
    }

    public function __construct(String $id) {
        $this->id = $id;
    }

    public function equal(?TeamId $id): bool {
        if ($id === null)
            return false;

        return $this->id === $id->value();
    }
}