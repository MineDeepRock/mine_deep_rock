<?php


namespace mine_deep_rock\pmmp\service;


use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_game_system\data_model\PlayerData;

class SendParticipantsToLobbyPMMPService
{
    /**
     * @param PlayerData[] $participants
     */
    static function execute(array $participants, TaskScheduler $taskScheduler): void {
        $server = Server::getInstance();
        $lobby = $server->getLevelByName("lobby");

        foreach ($participants as $participant) {
            $player = $server->getPlayer($participant);
            $player->teleport($lobby->getSpawnLocation());

            //ロビーに入るときはロビー用アイテムを渡す
            SendLobbyItemsPMMPService::execute($player, $taskScheduler);
        }
    }
}