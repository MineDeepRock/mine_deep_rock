<?php


namespace gun_system\models\attachment;


abstract class Attachment
{
    private $name;
    private $type;

    public function __construct(string $name,AttachmentType $type) {
        $this->name = $name;
        $this->type = $type;
    }
}