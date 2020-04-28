<?php


namespace game_system\model;


use ValueObject;

abstract class Game extends ValueObject
{
    private $id;
    protected $isStarted;

    public function __construct() {
        $this->id = GameId::asNew();
        $this->isStarted = false;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool {
        return $this->isStarted;
    }

}