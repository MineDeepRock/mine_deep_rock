<?php


namespace gun_system\models;


class KillCountCondition extends Condition
{
    private $weaponName;
    private $count;

    public function __construct(string $weaponName, int $count) {
        $this->weaponName = $weaponName;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getWeaponName(): string {
        return $this->weaponName;
    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->count;
    }
}
