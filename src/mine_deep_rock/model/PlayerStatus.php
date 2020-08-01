<?php


namespace mine_deep_rock\model;


use mine_deep_rock\store\MilitaryDepartmentsStore;

class PlayerStatus
{
    private $name;
    private $militaryDepartment;
    private $mainGunName;
    private $subGunName;

    private $money;

    public function __construct(string $name, MilitaryDepartment $militaryDepartment, string $mainGunName, string $subGunName, int $money) {
        $this->name = $name;
        $this->militaryDepartment = $militaryDepartment;
        $this->mainGunName = $mainGunName;
        $this->subGunName = $subGunName;
        $this->money = $money;
    }

    static function asNew(string $name): PlayerStatus {
        $militaryDepartment = MilitaryDepartmentsStore::get(MilitaryDepartment::AssaultSoldier);
        return new PlayerStatus($name, $militaryDepartment, $militaryDepartment->getDefaultGunName(), "Mle1903", 0);
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

    /**
     * @return int
     */
    public function getMoney(): int {
        return $this->money;
    }
}