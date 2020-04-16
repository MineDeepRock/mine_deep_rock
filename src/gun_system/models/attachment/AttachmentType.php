<?php


namespace gun_system\models\attachment;


class AttachmentType
{
    private $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public function equal(AttachmentType $gunType) :bool {
        return $this->type == $gunType->type;
    }

    public static function Scope():AttachmentType {
        return new AttachmentType("Scope");
    }

    public static function Barrel():AttachmentType {
        return new AttachmentType("Barrel");
    }
}