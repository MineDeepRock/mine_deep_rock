<?php


namespace mine_deep_rock\model;


use mine_deep_rock\model\skill\normal\Frack;
use mine_deep_rock\store\MilitaryDepartmentsStore;

class PlayerStatus
{
    private $name;
    private $militaryDepartment;
    private $mainGunName;
    private $subGunName;

    /**
     * @var Skill[]
     */
    private $owningSkills;
    /**
     * @var Skill[]
     */
    private $selectedSkills;

    private $money;

    public function __construct(string $name, MilitaryDepartment $militaryDepartment, string $mainGunName, string $subGunName, array $owningSkills, array $selectedSkills, int $money) {
        $this->name = $name;
        $this->militaryDepartment = $militaryDepartment;
        $this->mainGunName = $mainGunName;
        $this->subGunName = $subGunName;
        $this->owningSkills = $owningSkills;
        $this->selectedSkills = $selectedSkills;
        $this->money = $money;
    }

    static function asNew(string $name): PlayerStatus {
        $militaryDepartment = MilitaryDepartmentsStore::get(MilitaryDepartment::AssaultSoldier);
        return new PlayerStatus(
            $name,
            $militaryDepartment,
            $militaryDepartment->getDefaultGunName(),
            "Mle1903",
            [
                new Frack(),
            ],
            [],
            3000);
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

    /**
     * @return array
     */
    public function getOwningSkills(): array {
        return $this->owningSkills;
    }

    /**
     * @return array
     */
    public function getSelectedSkills(): array {
        return $this->selectedSkills;
    }

    public function isSelectedSkill(Skill $skill): bool {
        foreach ($this->selectedSkills as $selectedSkill) {
            if ($selectedSkill::Name === $skill::Name) {
                return true;
            }
        }

        return false;
    }
}