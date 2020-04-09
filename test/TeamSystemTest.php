<?php

use PHPUnit\Framework\TestCase;
use team_system\models\Player;
use team_system\models\TeamId;
use team_system\service\PlayerService;
use team_system\service\TeamService;


class  TeamSystemTest extends TestCase
{
    private $playerName = "Bob";

    //参加時
    public function testInitPlayer() {

        $service = new PlayerService();
        $service->init($this->playerName);

        $player = $service->getData($this->playerName);

        $this->assertEquals(new Player("Bob"), $player);
    }

    //チーム作成
    //チーム検索
    public function testCreateTeam() {

        $playerService = new PlayerService();
        $teamService = new TeamService();

        $player = $playerService->getData($this->playerName);

        $createdTeam = $teamService->create($player)->getValue();

        $this->assertEquals($createdTeam, $teamService->searchAtOwner($player));
        $this->assertEquals($createdTeam, $teamService->searchAtId($createdTeam->getId()));
    }

    //すでに作成済み
    public function testAlreadyCreated() {

        $playerService = new PlayerService();
        $teamService = new TeamService();

        $player = $playerService->getData($this->playerName);

        $result = $teamService->create($player);

        $this->assertEquals(false, $result->isSucceed());
        $this->assertEquals("すでにチームを作っています", $result->getValue()->getMessage());
    }
}