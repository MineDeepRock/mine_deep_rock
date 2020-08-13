<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;

class SendKillMessagePMMPService
{
    static function execute(Player $attacker, Player $victim): void {
        $attacker->sendTip($victim->getName() . "を倒した");
        $victim->sendTip($attacker->getName() . "にやられた");
    }
}