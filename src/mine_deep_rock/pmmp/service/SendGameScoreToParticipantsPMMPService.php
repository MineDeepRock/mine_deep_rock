<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\Server;

class SendGameScoreToParticipantsPMMPService
{
    static function execute(array $participants): void {
        $server = Server::getInstance();

        //TODO:参加者全員に全員のスコアリストを送るようにする
        foreach ($participants as $participant) {
            $participantGameStatus = PlayerGameStatusStore::findByName($participant->getName());
            $player = $server->getPlayer($participant->getName());
            $player->sendMessage("あなたのkill数" . $participantGameStatus->getKillCountInGame());
        }
    }
}