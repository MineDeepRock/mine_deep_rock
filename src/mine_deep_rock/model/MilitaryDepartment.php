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

    public function __construct(string $name, array $gunTypes, string $defaultGunName, array $boxes, array $effectInstances, array $grenades) {
        $this->name = $name;
        $this->gunTypes = $gunTypes;
        $this->defaultGunName = $defaultGunName;
        $this->boxes = $boxes;
        $this->effectInstances = $effectInstances;
        $this->grenades = $grenades;
    }
}