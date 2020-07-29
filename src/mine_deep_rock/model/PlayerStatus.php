<?php


namespace mine_deep_rock\model;


class PlayerStatus
{
    private $name;
    private $militaryDepartment;

    public function __construct(string $name,MilitaryDepartment $militaryDepartment) {
        $this->name = $name;
        $this->militaryDepartment = $militaryDepartment;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return MilitaryDepartment
     */
    public function getMilitaryDepartment(): MilitaryDepartment {
        return $this->militaryDepartment;
    }
}