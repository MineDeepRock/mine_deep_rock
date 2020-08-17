<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\model\DominationFlagData;
use pocketmine\level\Position;

class DominationFlagDataJsonAdapter
{
    static function encode(DominationFlagData $flagData): array {
        $pos = $flagData->getPosition();
        return [
            "name" => $flagData->getName(),
            "position" => [
                "x" => $pos->getX(),
                "y" => $pos->getY(),
                "z" => $pos->getZ(),
            ]
        ];
    }

    static function decode(array $json): DominationFlagData {
        $pos = $json["position"];
        return new DominationFlagData(
            $json["name"],
            new Position(
                $pos["x"],
                $pos["y"],
                $pos["z"]
            )
        );
    }
}