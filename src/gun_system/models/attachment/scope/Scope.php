<?php


namespace gun_system\models\attachment\scope;


use gun_system\models\attachment\Attachment;
use gun_system\models\attachment\AttachmentType;

abstract class Scope extends Attachment
{
    private $magnification;

    public function __construct(string $name,Magnification $magnification) {
        $this->magnification = $magnification;
        parent::__construct($name, AttachmentType::Scope());
    }

    /**
     * @param mixed $magnification
     */
    public function setMagnification(Magnification $magnification): void {
        $this->magnification = $magnification;
    }
}

class Magnification
{
    private $value;

    public function __construct(int $value) {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }
}