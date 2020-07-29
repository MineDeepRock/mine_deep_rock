<?php


namespace mine_deep_rock\store;


use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\service\LoadMilitaryDepartmentsService;

class MilitaryDepartmentsStore
{
    /**
     * @var MilitaryDepartment[]
     */
    static private $militaryDepartments = [];

    static function init() {
        self::$militaryDepartments = LoadMilitaryDepartmentsService::execute();
    }

    static function getAll(): array {
        return self::$militaryDepartments;
    }

    static function get(string $name): ?MilitaryDepartment {
        foreach (self::$militaryDepartments as $militaryDepartment) {
            if ($militaryDepartment->getName() === $militaryDepartment) return $militaryDepartment;
        }

        return null;
    }
}