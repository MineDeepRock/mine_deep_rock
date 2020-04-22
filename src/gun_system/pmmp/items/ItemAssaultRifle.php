<?php


namespace gun_system\pmmp\items;


use gun_system\models\assault_rifle\AssaultRifle;
use gun_system\models\assault_rifle\attachiment\magazine\AssaultRifleMagazine;
use gun_system\models\assault_rifle\attachiment\scope\AssaultRifleScope;
use pocketmine\Player;

class ItemAssaultRifle extends ItemGun
{
    public function __construct(string $name, AssaultRifle $gun, Player $owner) { parent::__construct($name, $gun, $owner); }

    public function setScope(AssaultRifleScope $scope): void {
        $this->gun->setScope($scope);
    }

    public function setMagazine(AssaultRifleMagazine $magazine): void {
        $this->gun->setMagazine($magazine);
    }
}