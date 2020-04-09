<?php

//帰ってくる結果に[成功か失敗か]の情報を含めたい場合は、ServiceResultを使う
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
     * @param null $value
     */
    public function __construct(bool $isSucceed, $value = null) {
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