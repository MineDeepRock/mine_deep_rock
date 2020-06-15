<?php


namespace mine_deep_rock\scoreboards;


use scoreboard_system\models\Score;
use scoreboard_system\models\Scoreboard;
use scoreboard_system\models\ScoreboardSlot;
use scoreboard_system\models\ScoreSortType;

class LobbyScoreboard extends Scoreboard
{
    public function __construct(int $gameParticipantsCount) {
        $slot = ScoreboardSlot::sideBar();
        $scores = [
            new Score($slot, "=========", 0, 0),
            new Score($slot, "参加人数:" . $gameParticipantsCount, 1, 1),
        ];
        parent::__construct($slot, "MineDeepRock", $scores, ScoreSortType::smallToLarge());
    }
}