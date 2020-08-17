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
        if (count($playersData) === 0) return;

        $teamPlayersCount = [];
        foreach ($playersData as $playerData) {
            $key = strval($playerData->getTeamId());
            if (!array_key_exists($key, $teamPlayersCount)) {
                $teamPlayersCount[$key] = 0;
            }

            $teamPlayersCount[$key]++;
        }

        arsort($teamPlayersCount);

        $keys = array_keys($teamPlayersCount);


        if (count($teamPlayersCount) === 1) {
            $flag->makeProgress(new TeamId($keys[0]), 5 * $teamPlayersCount[$keys[0]]);
        } else {
            $difference = $teamPlayersCount[$keys[0]] - $teamPlayersCount[$keys[1]];
            if ($difference === 0) return;
            $flag->makeProgress(new TeamId($keys[0]), 5 * $difference);
        }
    }
}