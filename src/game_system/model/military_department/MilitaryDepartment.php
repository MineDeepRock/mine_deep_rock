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
}