<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\attachiment\magazine\SubMachineGunMagazine;
use gun_system\models\sub_machine_gun\attachment\scope\SubMachineGunScope;
use gun_system\models\sub_machine_gun\SubMachineGun;
use pocketmine\Player;

class ItemSubMachineGun extends ItemGun
{
    public function __construct(string $name, SubMachineGun $gun, Player $owner) { parent::__construct($name, $gun, $owner); }

    public function setScope(SubMachineGunScope $scope): void {
        $this->gun->setScope($scope);
    }
    public function setMagazine(SubMachineGunMagazine $magazine): void {
        $this->gun->setMagazine($magazine);
    }
}