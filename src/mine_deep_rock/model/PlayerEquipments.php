<?php


namespace mine_deep_rock\model;


use mine_deep_rock\store\MilitaryDepartmentsStore;

class PlayerEquipments
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var MilitaryDepartment
     */
    private $militaryDepartment;
    /**
     * @var string
     */
    private $mainGunName;
    /**
     * @var string
     */
    private $subGunName;
    /**
     * @var Skill[]
     */
    private $selectedSkills;

    public function __construct(string $name, MilitaryDepartment $militaryDepartment, string $mainGunName, string $subGunName, array $selectedSkills) {
        $this->name = $name;
        $this->militaryDepartment = $militaryDepartment;
        $this->mainGunName = $mainGunName;
        $this->subGunName = $subGunName;
        $this->selectedSkills = $selectedSkills;
    }

    static function asNew(string $name): PlayerEquipments {
        $militaryDepartment = MilitaryDepartmentsStore::get(MilitaryDepartment::AssaultSoldier);
        return new PlayerEquipments(
            $name,
            $militaryDepartment,
            $militaryDepartment->getDefaultGunName(),
            "Mle1903",
            []);
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
     * @return Skill[]
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