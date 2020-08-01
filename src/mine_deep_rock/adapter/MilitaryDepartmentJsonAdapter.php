<?php


namespace mine_deep_rock\adapter;


use box_system\models\Box;
use grenade_system\models\Grenade;
use gun_system\model\GunType;
use mine_deep_rock\model\MilitaryDepartment;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

class MilitaryDepartmentJsonAdapter
{
    static function decode(array $json): MilitaryDepartment {
        $gunTypes = array_map(function (string $name) {
            return GunType::fromString($name);
        }, $json["gun_types"]);

        $boxes = array_map(function (string $name) {
            return Box::fromString($name);
        }, $json["boxes"]);

        $effectInstances = array_map(function (array $effectInstance) {
            return new EffectInstance(Effect::getEffect($effectInstance["id"]), $effectInstance["duration"], $effectInstance["amplifier"], $effectInstance["visible"]);
        }, $json["effect_instances"]);

        $grenades = array_map(function (string $name) {
            return Grenade::fromString($name);
        }, $json["grenades"]);

        return new MilitaryDepartment(
            $json["name"],
            $gunTypes,
            $json["default_gun_name"],
            $boxes,
            $effectInstances,
            $grenades
        );
    }
}