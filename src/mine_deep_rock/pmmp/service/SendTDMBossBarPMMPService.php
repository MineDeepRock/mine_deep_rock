<?php


namespace mine_deep_rock\pmmp\service;


use bossbar_system\models\BossBar;
use LogicException;
use pocketmine\Server;
use team_game_system\data_model\PlayerData;

class SendTDMBossBarPMMPService
{
    /**
     * @param PlayerData[] $participants
     * @param int $timeLimit
     * @param int $elapsedTime
     */
    static function execute(array $participants, ?int $timeLimit, int $elapsedTime): void {
        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());
            $bossBar = BossBar::get($player);

            if ($bossBar === null) {
                $bossBar = new BossBar("", 1);
                $bossBar->send($player);
            }

            if ($timeLimit === null) {
                $bossBar->updateTitle($player, "経過時間:" . $elapsedTime);
            } else {
                $bossBar->updateTitle($player, "残り時間:" . ($timeLimit - $elapsedTime));
                $bossBar->updatePercentage($player, $elapsedTime / $timeLimit);
            }
        }
    }
}