<?php

use PHPUnit\Framework\TestCase;
use team_system\TeamSystemClient;
use team_system\models\Player;
use team_system\service\PlayerService;
use team_system\service\TeamService;

class  TeamSystemTest extends TestCase
{
    private $teamOwner = "TeamOwner";

    private $firstPlayerName = "Mike";
    private $secondPlayerName = "Steve";
    private $thirdPlayerName = "Alex";

    private $otherTeamOwner = "OtherTeamOwner";

    private $otherTeamPlayerName = "Ichiro";

    //参加時
    public function testInitPlayer() {

        $service = new PlayerService();
        $service->init($this->teamOwner);
        $service->init($this->firstPlayerName);
        $service->init($this->secondPlayerName);
        $service->init($this->thirdPlayerName);

        $service->init($this->otherTeamOwner);
        $service->init($this->otherTeamPlayerName);

        $player = $service->getData($this->teamOwner);

        $this->assertEquals(new Player("TeamOwner"), $player);
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
        $client->create($this->teamOwner, function ($message) {
            $this->assertEquals("チームを作成しました", $message);
        });

        $client->create($this->otherTeamOwner, function ($message) {
            $this->assertEquals("チームを作成しました", $message);
        });
    }

    //チームに参加
    public function testJoin() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->join($this->firstPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });
        $client->join($this->secondPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });
        $client->join($this->thirdPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });
    }

    //すでに作成済み
    public function testAlreadyCreated() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->create($this->teamOwner, function ($message) {
            $this->assertEquals("すでにチームを作っています", $message);
        });
    }

    //すでにそのチームに参加している
    public function testAlreadyJoined() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->join($this->firstPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("すでにそのチームに参加しています", $message);
        });
    }

    //満員
    public function testJoinFullTeam() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->join($this->otherTeamPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("そのチームは満員です", $message);
        });
    }
}