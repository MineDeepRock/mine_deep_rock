<?php


namespace mine_deep_rock\pmmp\service;


use bossbar_system\models\BossBar;
use pocketmine\Server;
use team_game_system\data_model\PlayerData;

class UpdateTDMBossBarPMMPService
{
    /**
     * @param PlayerData[] $participants
     * @param int $timeLimit
     * @param int $elapsedTime
     */
    static function execute(array $participants, int $timeLimit, int $elapsedTime):void {
        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());
            $bossBar = BossBar::get($player);
            $bossBar->updateTitle($player, "残り時間:" . ($timeLimit - $elapsedTime));
            $bossBar->updatePercentage($player, $elapsedTime / $timeLimit);
        }
    }
}