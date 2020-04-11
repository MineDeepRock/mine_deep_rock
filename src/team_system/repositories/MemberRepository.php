<?php


namespace team_system\repositories;


use mysqli;
use Repository;
use team_system\models\Member;
use team_system\models\TeamId;

class MemberRepository extends Repository
{
    public function init(Member $member) {
        $memberName = $member->getName();
        if ($this->exists($memberName)) {
            $result = $this->db->query("DELETE FROM members WHERE name = '{$memberName}'");

            if (!$result) {
                $sql_error = $this->db->error;
                error_log($sql_error);
                die($sql_error);
            }
        }
        $result = $this->db->query("INSERT INTO members(name) VALUES('{$memberName}')");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }

    public function exists(string $name): bool {
        return $this->getData($name) != null;
    }

    public function getData(string $name): ?Member {
        $result = $this->db->query("SELECT * FROM members WHERE name='{$name}'");
        if ($result->num_rows === 0) {
            return null;
        }
        return Member::fromJson($result->fetch_assoc());
    }

    /**
     * @param string $name
     * @param TeamId|null $teamId
     */
    public function updateBelongTeamId(string $name, ?TeamId $teamId): void {
        $newTeamId = $teamId == null ? null : $teamId->value();

        $result = $this->db->query("UPDATE members SET belong_team_id='{$newTeamId}' WHERE name='{$name}'");

        if (!$result) {
            $sql_error = $this->db->error;
            error_log($sql_error);
            die($sql_error);
        }
    }
}