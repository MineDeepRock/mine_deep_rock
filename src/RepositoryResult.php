<?php

Class RepositoryResult  {

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
     * RepositoryResult constructor.
     * @param bool $isSucceed
     * @param $value
     */
    public function __construct(bool $isSucceed, $value) {
        $this->isSucceed = $isSucceed;
        $this->value = $value;
    }
}