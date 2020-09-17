<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\data_model\CandidateCorePositionsGroup;
use pocketmine\level\Level;
use pocketmine\level\Position;

class CandidateCorePositionsGroupJsonAdapter
{
    static function encode(CandidateCorePositionsGroup $group): array {
        return [array_map(function ($position) {
            return [
                "x" => $position->getX(),
                "y" => $position->getY(),
                "z" => $position->getZ()
            ];
        }, $group->getPositions())];
    }

    static function decode(Level $level, array $json): CandidateCorePositionsGroup {
        return new CandidateCorePositionsGroup(
            array_map(function ($position) use ($level) {
                return new Position($position["x"], $position["y"], $position["z"], $level);
            }, $json)
        );
    }
}