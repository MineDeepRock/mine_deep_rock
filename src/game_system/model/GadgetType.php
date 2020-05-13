<?php


namespace game_system\model;


class GadgetType
{
    private $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public function equal(GadgetType $gunType) :bool {
        return $this->type == $gunType->type;
    }

    public static function AmmoBox():GadgetType {
        return new GadgetType("AmmoBox");
    }
    public static function MedicineBox():GadgetType {
        return new GadgetType("MedicineBox");
    }
    public static function FlareBox():GadgetType {
        return new GadgetType("FlareBox");
    }
    public static function FragGrenade():GadgetType {
        return new GadgetType("FragGrenade");
    }
    /**
     * @return mixed
     */
    public function getTypeText() {
        return $this->type;
    }
}