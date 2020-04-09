<?php

use PHPUnit\Framework\TestCase;
use team_system\models\Player;
use team_system\models\TeamId;
use team_system\service\PlayerService;

class  TeamSystemTest extends TestCase
{
    private $player;

    //参加時
    public function testInitPlayer() {

        $service = new PlayerService();
        $service->init("Bob");

        $this->player = $service->getData("Bob");

        $this->assertEquals(new Player("Bob"), $this->player);
    }
}