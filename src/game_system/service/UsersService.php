<?php


namespace game_system\service;


use game_system\model\GameId;
use game_system\model\TeamId;
use game_system\model\User;
use Service;

class UsersService extends Service
{
    public function userLogin(string $userName): void {
        //TODO:存在すればそのまま、そうでなければ作成
    }

    public function getUserData(string $userName): User {
        //TODO:userNameからデータ取得
    }

    public function getParticipants(GameId $gameId): array {
        //TODO:participatedGameIdと比較して一致したら返す
    }

    public function joinGame(TeamId $redTeamId, TeamId $blueTeamId, string $userName): void {
        //TODO:belongTeamId,lastBelongTeamId,participatedGameIdをセット
        //TODO:チームを決める
    }

    public function quitGame(string $userName): void {
        //TODO:belongTeamId,participatedGameIdをnullに
    }

    public function addWinCount(string $userName): void {
        //TODO:winCountを更新する
    }
}