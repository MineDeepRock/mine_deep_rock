<?php


namespace game_system\model\military_department;


abstract class MilitaryDepartment
{
    private $name = "";
    private $canEquipGunTypes = [];
    private $canEquipGadgetTypes = [];
    private $effectIds = [];

    private $defaultWeaponName = "";

    public function __construct(
        string $name,
        array $canEquipGunTypes,
        array $canEquipGadgetTypes,
        array $effects,
        string $defaultWeaponName) {

        $this->name = $name;
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

    abstract function getDescription(): string;

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
}