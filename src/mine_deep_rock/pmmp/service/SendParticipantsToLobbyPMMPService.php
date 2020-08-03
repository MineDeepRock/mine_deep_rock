<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\scoreboard\PlayerStatusScoreboard;
use mine_deep_rock\pmmp\scoreboard\TDMScoreboard;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use scoreboard_system\models\Scoreboard;
use team_game_system\data_model\PlayerData;

class SendParticipantsToLobbyPMMPService
{
    /**
     * @param PlayerData[] $participants
     * @param TaskScheduler $taskScheduler
     */
    static function execute(array $participants, TaskScheduler $taskScheduler): void {
        $server = Server::getInstance();
        $lobby = $server->getLevelByName("lobby");

        foreach ($participants as $participant) {
            $player = $server->getPlayer($participant->getName());
            $player->teleport($lobby->getSpawnLocation());

            //ロビーに入るときはロビー用アイテムを渡す
            SendLobbyItemsPMMPService::execute($player, $taskScheduler);

            //TODO:TDMとは限らない
            TDMScoreboard::delete($player);
            PlayerStatusScoreboard::send($player);
        }
    }
}