<?php


namespace game_system\model;


class AmmoBox extends Box
{
    public function __construct(int $secondLimit) {
        parent::__construct($secondLimit);
    }

    public function addPlayerUsed(string $playerName, string $gunName): void {
        $this->playerUsed[] = [
            "name"=>$playerName,
            "gun"=>$gunName
        ];
    }

    public function isAlreadyUsed(string $playerName, string $gunName) :bool {
        return in_array(["name"=>$playerName, "gun"=>$gunName],$this->playerUsed);
    }
}