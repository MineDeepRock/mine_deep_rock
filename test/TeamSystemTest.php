<?php

use PHPUnit\Framework\TestCase;
use team_system\TeamSystemClient;
use team_system\models\Member;
use team_system\services\MemberService;
use team_system\services\TeamService;

class  TeamSystemTest extends TestCase
{
    private $teamLeader = "TeamLeader";

    private $firstMemberName = "first";
    private $secondMemberName = "second";
    private $thirdMemberName = "third";

    private $otherTeamLeader = "OtherTeamLeader";

    private $otherTeamMemberName = "Ichiro";

    private $freeMemberName = "FreeMan";


    //参加時
    //初期化
    public function testInitMember() {

        $service = new MemberService();
        $service->init($this->teamLeader);
        $service->init($this->firstMemberName);
        $service->init($this->secondMemberName);
        $service->init($this->thirdMemberName);

        $service->init($this->otherTeamLeader);
        $service->init($this->otherTeamMemberName);

        $service->init($this->freeMemberName);

        $member = $service->getData($this->teamLeader);

        $this->assertEquals(new Member("TeamLeader"), $member);
    }

    //存在しないチームに参加
    public function testJoinNotExist() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());

        $client->join($this->firstMemberName, "NotExist", function ($message) {
            $this->assertEquals("そのようなチームは存在しません", $message);
        });
    }

    //チーム作成
    public function testCreateTeam() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->create($this->teamLeader, function ($message) {
            $this->assertEquals("チームを作成しました", $message);
        });

        $client->create($this->otherTeamLeader, function ($message) {
            $this->assertEquals("チームを作成しました", $message);
        });
    }

    //チームに参加
    public function testJoin() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->join($this->firstMemberName, $this->teamLeader, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });
        $client->join($this->secondMemberName, $this->teamLeader, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });

        $client->join($this->otherTeamMemberName, $this->otherTeamLeader, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });
    }

    //すでに作成済み
    public function testAlreadyCreated() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->create($this->teamLeader, function ($message) {
            $this->assertEquals("すでにチームを作っています", $message);
        });
    }

    //すでにそのチームに参加している
    //参加
    //作成
    public function testAlreadyJoined() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->join($this->firstMemberName, $this->teamLeader, function ($message) {
            $this->assertEquals("すでにそのチームに参加しています", $message);
        });

        $client->create($this->firstMemberName, function ($message) {
            $this->assertEquals("すでに他のチームに参加しています", $message);
        });
    }

    //すでに他のチームに参加
    public function testAlreadyJoinedOtherTeam() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->join($this->otherTeamMemberName, $this->teamLeader, function ($message) {
            $this->assertEquals("あなたは他のチームに参加しています", $message);
        });
    }

    //満員
    public function testJoinFullTeam() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        //チームを満員にする
        $client->join($this->thirdMemberName, $this->teamLeader, function ($message) {
            $this->assertEquals("チームに参加しました", $message);
        });

        $client->join($this->otherTeamMemberName, $this->teamLeader, function ($message) {
            $this->assertEquals("そのチームは満員です", $message);
        });
    }

    //チームに参加していない
    //チームを譲る
    //チームを抜ける
    public function testNotBelongTeam() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->yield($this->freeMemberName, function ($message) {
            $this->assertEquals("あなたはオーナで無いか、チームに入っていません", $message);
        }, $this->secondMemberName);

        $client->quit($this->freeMemberName, function ($message) {
            $this->assertEquals("あなたはチームに参加していません", $message);
        });
    }

    //オーナーじゃないのに譲る
    public function testNotLeader() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->yield($this->firstMemberName, function ($message) {
            $this->assertEquals("あなたはオーナで無いか、チームに入っていません", $message);
        }, $this->secondMemberName);
    }

    //譲る相手がいない
    public function testNotExistsNextLeader() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->yield($this->teamLeader, function ($message) {
            $this->assertEquals("そのような名前のプレイヤーはチームにいません", $message);
        }, "NotExists");
    }

    //チームを抜ける
    public function testQuitTeam() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->quit($this->otherTeamMemberName, function ($message) {
            $this->assertEquals("チームを抜けました", $message);
        });
    }

    //チームに自分しかいない
    public function testNotExistsCoworker() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->yield($this->otherTeamLeader, function ($message) {
            $this->assertEquals("譲る相手がいなかったので、チームを削除しました", $message);
        });
    }

    //チームを譲る
    public function testYield() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->yield($this->teamLeader, function ($message) {
            $this->assertEquals("チームのオーナーを{$this->secondMemberName}に譲りました", $message);
        },$this->secondMemberName);
    }

    //オーナーがチームを抜ける
    public function testLeaderQuitTeam() {
        $client = new TeamSystemClient(new TeamService(), new MemberService());
        $client->quit($this->secondMemberName, function ($message) {
            $this->assertEquals("チームを抜けました", $message);
        });
    }
}