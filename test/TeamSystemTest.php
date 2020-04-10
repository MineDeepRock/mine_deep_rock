<?php

use PHPUnit\Framework\TestCase;
use team_system\TeamSystemClient;
use team_system\models\Player;
use team_system\service\PlayerService;
use team_system\service\TeamService;

class  TeamSystemTest extends TestCase
{
    private $firstPlayerName = "Bob";

    private $secondPlayerName = "Mike";

    //参加時
    public function testInitPlayer() {

        $service = new PlayerService();
        $service->init($this->firstPlayerName);
        $service->init($this->secondPlayerName);

        $player = $service->getData($this->firstPlayerName);

        $this->assertEquals(new Player("Bob"), $player);
    }

    //存在しないチームに参加
    public function testJoinNotExist() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());

        $client->join($this->firstPlayerName, "NotExist", function ($message) {
            $this->assertEquals("そのようなチームは存在しません", $message);
        });
    }

    //チーム作成
    public function testCreateTeam() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->create($this->firstPlayerName, function ($message) {
            $this->assertEquals("チームを作成しました", $message);
        });
    }

    //チームに参加
    public function testJoin() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->join($this->secondPlayerName, $this->firstPlayerName, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });
    }

    //すでに作成済み
    public function testAlreadyCreated() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->create($this->firstPlayerName, function ($message) {
            $this->assertEquals("すでにチームを作っています", $message);
        });
    }

    //すでに参加済み
    public function testAlyreadyJoined() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->join($this->secondPlayerName, $this->firstPlayerName, function ($message) {
            $this->assertEquals("すでにそのチームに参加しています", $message);
        });
    }
}