<?php


namespace mine_deep_rock\service;


use mine_deep_rock\store\TDMGameIds;
use pocketmine\utils\Color;
use team_game_system\model\Game;
use team_game_system\model\Score;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class CreateTDM
{

    static function execute(): void {
        $teams = [
            Team::asNew("Red", new Color(255, 0, 0)),
            Team::asNew("Blue", new Color(0, 0, 255)),
        ];
        $maxScore = new Score(25);
        //TODO:マップ選択
        $map = TeamGameSystem::selectMap("", $teams);
        $game = Game::asNew($map, $teams, $maxScore, null, 300);

        TeamGameSystem::registerGame($game);
        TDMGameIds::add($game->getId());
    }
}