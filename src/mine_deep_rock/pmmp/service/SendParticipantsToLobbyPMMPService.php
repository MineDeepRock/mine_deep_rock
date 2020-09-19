<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\GameTypeList;
use mine_deep_rock\pmmp\scoreboard\CorePvPGameScoreboard;
use mine_deep_rock\pmmp\scoreboard\DominationScoreboard;
use mine_deep_rock\pmmp\scoreboard\OneOnOneScoreboard;
use mine_deep_rock\pmmp\scoreboard\PlayerStatusScoreboard;
use mine_deep_rock\pmmp\scoreboard\TDMScoreboard;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_game_system\data_model\PlayerData;
use team_game_system\model\GameType;

class SendParticipantsToLobbyPMMPService
{
    /**
     * @param GameType $gameType
     * @param PlayerData[] $participants
     * @param TaskScheduler $taskScheduler
     */
    static function execute(GameType $gameType, array $participants, TaskScheduler $taskScheduler): void {
        $server = Server::getInstance();
        $lobby = $server->getLevelByName("lobby");

        foreach ($participants as $participant) {
            $player = $server->getPlayer($participant->getName());
            if ($player !== null) {
                if ($player->isOnline()) {
                    $player->teleport($lobby->getSpawnLocation());

                    //ロビーに入るときはロビー用アイテムを渡す
                    SendLobbyItemsPMMPService::execute($player, $taskScheduler);

                    TDMScoreboard::delete($player);
                    if ($gameType->equals(GameTypeList::TDM())) {
                        TDMScoreboard::delete($player);
                    } else if ($gameType->equals(GameTypeList::Domination())) {
                        DominationScoreboard::delete($player);
                    } else if ($gameType->equals(GameTypeList::OneOnOne())) {
                        OneOnOneScoreboard::delete($player);
                    } else if ($gameType->equals(GameTypeList::OneOnOne())) {
                        CorePvPGameScoreboard::delete($player);
                    }

                    PlayerStatusScoreboard::send($player);
                }
            }
        }
    }
}