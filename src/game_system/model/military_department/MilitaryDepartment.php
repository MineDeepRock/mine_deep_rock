<?php


namespace game_system\model\military_department;


abstract class MilitaryDepartment
{
    private $name = "";
    private $canEquipGunTypes = [];
    private $canEquipGadgetTypes = [];
    private $effectIds = [];

    public function __construct(
        string $name,
        array $canEquipGunTypes,
        array $canEquipGadgetTypes,
        array $effectIds) {

        $this->name = $name;
        $this->canEquipGunTypes = $canEquipGunTypes;
        $this->canEquipGadgetTypes = $canEquipGadgetTypes;
        $this->effectIds = $effectIds;
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
    public function getEffectIds(): array {
        return $this->effectIds;
    }
}