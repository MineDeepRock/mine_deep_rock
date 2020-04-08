<?php

Class ServiceResult
{

    private $isSucceed;
    private $value;

    /**
     * @return bool
     */
    public function isSucceed(): bool {
        return $this->isSucceed;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * ServiceResult constructor.
     * @param bool $isSucceed
     * @param $value
     */
    public function __construct(bool $isSucceed, $value) {
        $this->isSucceed = $isSucceed;
        $this->value = $value;
    }
}

class ServiceErrorMessage
{
    private $message;

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    public function __construct($message) {
        $this->message = $message;
    }
}