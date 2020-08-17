<?php


namespace mine_deep_rock\service;


use mine_deep_rock\model\DominationFlag;
use team_game_system\data_model\PlayerData;
use team_game_system\model\TeamId;

class OccupyFlagService
{
    /**
     * @param DominationFlag $flag
     * @param PlayerData[] $playersData
     */
    static function execute(DominationFlag $flag, array $playersData): void {
        $teamPlayersCount = [];
        foreach ($playersData as $playerData) {
            $key = strval($playerData->getTeamId());
            if (array_key_exists($key, $teamPlayersCount)) {
                $teamPlayersCount[$key] = 0;
            }

            $teamPlayersCount[$key]++;
        }

        arsort($teamPlayersCount);


        //TODO
        if (count($teamPlayersCount) < 2) return;

        $keys = array_keys($teamPlayersCount);
        $difference = $teamPlayersCount[$keys[0]] - $teamPlayersCount[$keys[1]];
        if ($difference === 0) return;

        $flag->makeProgress(new TeamId($keys[0]), 5 * $difference);
    }
}