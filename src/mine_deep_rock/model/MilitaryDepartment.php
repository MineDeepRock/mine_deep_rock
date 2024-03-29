<?php


namespace mine_deep_rock\model;


use box_system\models\Box;
use grenade_system\models\Grenade;
use gun_system\model\GunType;
use pocketmine\entity\EffectInstance;

class MilitaryDepartment
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $nameJp;
    /**
     * @var GunType[]
     */
    private $gunTypes;
    /**
     * @var string
     */
    private $defaultGunName;
    /**
     * @var Box[]
     */
    private $boxes;
    /**
     * @var Grenade[]
     */
    private $grenades;
    /**
     * @var EffectInstance[]
     */
    private $effectInstances;

    /**
     * @var Skill[]
     */
    private $skills;

    public function __construct(string $name,string $nameJp, array $gunTypes, string $defaultGunName, array $boxes, array $effectInstances, array $grenades, array $skills) {
        $this->name = $name;
        $this->nameJp = $nameJp;
        $this->gunTypes = $gunTypes;
        $this->defaultGunName = $defaultGunName;
        $this->boxes = $boxes;
        $this->effectInstances = $effectInstances;
        $this->grenades = $grenades;
        $this->skills = $skills;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return GunType[]
     */
    public function getGunTypes(): array {
        return $this->gunTypes;
    }

    /**
     * @return string
     */
    public function getDefaultGunName(): string {
        return $this->defaultGunName;
    }

    /**
     * @return Box[]
     */
    public function getBoxes(): array {
        return $this->boxes;
    }

    /**
     * @return Grenade[]
     */
    public function getGrenades(): array {
        return $this->grenades;
    }

    /**
     * @return EffectInstance[]
     */
    public function getEffectInstances(): array {
        return $this->effectInstances;
    }

    const AssaultSoldier = "AssaultSoldier";
    const NursingSoldier = "NursingSoldier";
    const Engineer = "Engineer";
    const Scout = "Scout";
    const Sentry = "Sentry";

    /**
     * @return Skill[]
     */
    public function getSkills(): array {
        return $this->skills;
    }

    public function canEquipSkill(Skill $target): bool {
        foreach ($this->skills as $skill) {
            if ($skill::Name === $target::Name) return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getNameJp(): string {
        return $this->nameJp;
    }
}