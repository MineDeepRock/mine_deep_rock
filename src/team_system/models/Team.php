<?php

namespace team_system\models;


class Team
{
    private $id;
    private $owner;
    private $members;

    public function __construct(string $id, Player $owner, array $members = []) {
        $this->id = $id;
        $this->owner = $owner;
        $this->members = $members;
    }

    public static function asNew(Player $owner) {
        $id = new TeamId();
        return new Team($id, $owner);
    }

    public static function fromJson(array $json) {
        $id = $json["id"];
        $owner =  new Player($json["owner"]);
        $members = $json["members"];

        return new Team($id,$owner,$members);

    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return Player
     */
    public function getOwner(): Player {
        return $this->owner;
    }

    public function join() {
        //TODO:参加
    }
}


class TeamId
{
    private $id;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    public function __construct() {
        $this->id = uniqid();
    }

}