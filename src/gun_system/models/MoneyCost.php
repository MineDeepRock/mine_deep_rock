<?php


namespace gun_system\models;


class MoneyCost extends Condition
{
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
}