<?php


namespace game_system\model\military_department;


abstract class MilitaryDepartment
{
    private $name = "";
    private $jpaName = "";
    private $canEquipGunTypes = [];
    private $canEquipGadgetTypes = [];
    private $effectIds = [];

    private $defaultWeaponName = "";

    public function __construct(
        string $name,
        string $jpaName,
        array $canEquipGunTypes,
        array $canEquipGadgetTypes,
        array $effects,
        string $defaultWeaponName) {

        $this->name = $name;
        $this->jpaName = $jpaName;
        $this->canEquipGunTypes = $canEquipGunTypes;
        $this->canEquipGadgetTypes = $canEquipGadgetTypes;
        $this->effectIds = $effects;
        $this->defaultWeaponName = $defaultWeaponName;
    }

    static function fromName(string $name): ?MilitaryDepartment {
        switch ($name) {
            case "AssaultSoldier":
                return new AssaultSoldier();
                break;
            case "Engineer":
                return new Engineer();
                break;
            case "NursingSoldier":
                return new NursingSoldier();
                break;
            case "Scout":
                return new Scout();
                break;
        }
        return new AssaultSoldier();
    }

    public function getDescription(): string {
        $text = "";
        $text .= "武器:";
        foreach ($this->canEquipGunTypes as $gunType)
            $text .= $gunType->getTypeText() . ",";
        $text .= "\n";

        $text .= "ガジェット:";
        foreach ($this->canEquipGadgetTypes as $gadgetType)
            $text .= $gadgetType->getTypeText() . ",";
        $text .= "\n";

        $text .= "エフェクト:";
        foreach ($this->effectIds as $effectId)
            $text .= $effectId->getType()->getName() . ",";
        return $text;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getCanEquipGunTypes(): array {
        return $this->canEquipGunTypes;
    }

    /**
     * @return array
     */
    public function getCanEquipGadgetTypes(): array {
        return $this->canEquipGadgetTypes;
    }

    /**
     * @return array
     */
    public function getEffects(): array {
        return $this->effectIds;
    }

    /**
     * @return string
     */
    public function getDefaultWeaponName(): string {
        return $this->defaultWeaponName;
    }

    /**
     * @return string
     */
    public function getJpaName(): string {
        return $this->jpaName;
    }
}