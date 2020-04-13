<?php


namespace main_system\models;


use Closure;

class Game
{
    private $teamId;

    private $isStarted;

    private $firstMap;
    private $secondMap;
    private $thirdMap;

    private $onFinished;

    public function __construct(string $teamId, Map $firstMap, Map $secondMap, Map $thirdMap,Closure $onFinished) {
        $this->teamId = $teamId;

        $this->firstMap = $firstMap;
        $this->secondMap = $secondMap;
        $this->thirdMap = $thirdMap;
        $this->onFinished = $onFinished;
    }

    public function start() {
        $this->isStarted = true;
    }

    public function end(){
        ($this->onFinished)();
    }
}