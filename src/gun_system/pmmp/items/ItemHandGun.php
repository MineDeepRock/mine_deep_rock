<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\attachiment\magazine\HandGunMagazine;
use gun_system\models\hand_gun\attachment\scope\HandGunScope;
use gun_system\models\hand_gun\HandGun;
use pocketmine\Player;

class ItemHandGun extends ItemGun
{
    public function __construct(string $name, HandGun $gun, Player $owner) { parent::__construct($name, $gun, $owner); }

    public function setScope(HandGunScope $scope): void {
        $this->gun->setScope($scope);
    }
    public function setMagazine(HandGunMagazine $magazine): void {
        $this->gun->setMagazine($magazine);
    }
}