<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\TeamGameSystem;

class SendKillLogPMMPService
{
    static function execute(Player $attacker, Player $victim): void {
        $victimData = TeamGameSystem::getPlayerData($victim);
        $attackerData = TeamGameSystem::getPlayerData($attacker);

        $participants = TeamGameSystem::getGamePlayersData($victimData->getGameId());
        $server = Server::getInstance();

        $weapon = $attacker->getInventory()->getItemInHand();

        $gameID = $victimData->getGameId();

        $attackerTeam = TeamGameSystem::getTeam($gameID, $attackerData->getTeamId());
        $victimTeam = TeamGameSystem::getTeam($gameID, $victimData->getTeamId());;

        if ($attackerTeam === null || $victimTeam === null) return;


        $message = $attackerTeam->getTeamColorFormat() . "[{$attacker->getName()}] " . TextFormat::WHITE . "{$weapon->getCustomName()} " . $victimTeam->getTeamColorFormat() . " [{$victim->getName()}]";

        foreach ($participants as $participant) {
            $player = $server->getPlayer($participant->getName());
            $player->sendMessage($message);
        }
    }
}