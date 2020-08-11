<?php


namespace mine_deep_rock\pmmp\service;


use bossbar_system\BossBar;
use bossbar_system\model\BossBarType;
use mine_deep_rock\pmmp\BossBarTypes;
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
            $bossBar = BossBar::findByType($player, BossBarTypes::TDM());

            if ($bossBar === null) {
                $bossBar = new BossBar($player, BossBarTypes::TDM(), "", 0);
                $bossBar->send();
            }

            if ($timeLimit === null) {
                $bossBar->updateTitle("経過時間:" . $elapsedTime);
            } else {
                $bossBar->updateTitle("残り時間:" . ($timeLimit - $elapsedTime));
                $bossBar->updatePercentage($elapsedTime / $timeLimit);
            }
        }
    }
}