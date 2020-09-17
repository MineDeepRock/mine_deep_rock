<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\data_model\CandidateCorePositionsGroup;
use pocketmine\level\Level;
use pocketmine\level\Position;

class CandidateCorePositionsGroupJsonAdapter
{
    static function encode(CandidateCorePositionsGroup $group): array {
        $positions = [];

        foreach ($group->getPositions() as $position) {
            $positions[] = [
                "x" => $position->getX(),
                "y" => $position->getY(),
                "z" => $position->getZ()
            ];
        }

        return $positions;
    }

    static function decode(Level $level, array $json): CandidateCorePositionsGroup {
        $positions = [];

        foreach ($json as $value) {
            $positions[] = new Position($value["x"], $value["y"], $value["z"], $level);
        }

        return new CandidateCorePositionsGroup($positions);
    }
}