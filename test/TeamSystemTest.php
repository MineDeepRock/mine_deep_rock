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

    private $freePlayerName = "FreeMan";


    //参加時
    public function testInitPlayer() {

        $service = new PlayerService();
        $service->init($this->teamOwner);
        $service->init($this->firstPlayerName);
        $service->init($this->secondPlayerName);
        $service->init($this->thirdPlayerName);

        $service->init($this->otherTeamOwner);
        $service->init($this->otherTeamPlayerName);

        $service->init($this->freePlayerName);

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

        $client->join($this->otherTeamPlayerName, $this->otherTeamOwner, function ($message) {
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

        $client->create($this->firstPlayerName, function ($message) {
            $this->assertEquals("すでに他のチームに参加しています", $message);
        });
    }

    //すでに他のチームに参加
    public function testAlreadyJoinedOtherTeam() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->join($this->otherTeamPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("あなたは他のチームに参加しています", $message);
        });

        //チームを満員にする
        $client->join($this->thirdPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });
    }

    //満員
    public function testJoinFullTeam() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->join($this->otherTeamPlayerName, $this->teamOwner, function ($message) {
            $this->assertEquals("そのチームは満員です", $message);
        });
    }

    //チームに参加していない
    public function testNotBelongTeam() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->yield($this->freePlayerName, function ($message) {
            $this->assertEquals("あなたはオーナで無いか、チームに入っていません", $message);
        }, $this->secondPlayerName);
    }

    //オーナーじゃないのに譲る
    public function testNotOwner() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->yield($this->firstPlayerName, function ($message) {
            $this->assertEquals("あなたはオーナで無いか、チームに入っていません", $message);
        }, $this->secondPlayerName);
    }

    //譲る相手がいない
    public function testNotExistsNextOwner() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->yield($this->teamOwner, function ($message) {
            $this->assertEquals("そのような名前のプレイヤーはチームにいません", $message);
        }, "NotExists");
    }

    //チームを抜ける
    public function testQuitTeam() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->quit($this->otherTeamPlayerName,function ($message) {
            $this->assertEquals("チームを抜けました", $message);
        });
    }

    //チームに自分しかいない
    public function testNotExistsCoworker() {
        $client = new TeamSystemClient(new TeamService(), new PlayerService());
        $client->yield($this->otherTeamOwner, function ($message) {
            $this->assertEquals("譲る相手がいなかったので、チームを削除しました", $message);
        });
    }
}