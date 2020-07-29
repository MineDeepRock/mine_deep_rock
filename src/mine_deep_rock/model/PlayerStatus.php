<?php


namespace mine_deep_rock\model;


class PlayerStatus
{
    private $name;
    private $militaryDepartment;
    private $mainGunName;
    private $subGunName;

    public function __construct(string $name, MilitaryDepartment $militaryDepartment, string $mainGunName, string $subGunName) {
        $this->name = $name;
        $this->militaryDepartment = $militaryDepartment;
        $this->mainGunName = $mainGunName;
        $this->subGunName = $subGunName;
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

    /**
     * @return string
     */
    public function getMainGunName(): string {
        return $this->mainGunName;
    }

    /**
     * @return string
     */
    public function getSubGunName(): string {
        return $this->subGunName;
    }
}