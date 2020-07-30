<?php


namespace mine_deep_rock\adapter;


use mine_deep_rock\model\GunRecord;

class GunRecordJsonAdapter
{
    static function decode(array $json): GunRecord {
        return new GunRecord(
            $json["name"],
            $json["kill_count"],
            $json["scope_magnification"]
        );
    }

    static function encode(GunRecord $gunRecord): array {
        return [
            "name" => $gunRecord->getName(),
            "kill_count" => $gunRecord->getKillCount(),
            "scope_magnification" => $gunRecord->getScopeMagnification(),
        ];
    }
}