<?php


namespace mine_deep_rock\pmmp\service;


use bossbar_system\BossBar;
use mine_deep_rock\GameTypeList;
use mine_deep_rock\pmmp\BossBarTypes;
use pocketmine\Server;
use team_game_system\data_model\PlayerData;
use team_game_system\model\GameType;

class SendBossBarOnGamePMMPService
{
    /**
     * @param GameType $gameType
     * @param PlayerData[] $participants
     * @param int $timeLimit
     * @param int $elapsedTime
     */
    static function execute(GameType $gameType, array $participants, ?int $timeLimit, int $elapsedTime): void {
        $bossBarType = BossBarTypes::fromGameType($gameType);
        foreach ($participants as $participant) {
            $player = Server::getInstance()->getPlayer($participant->getName());
            $bossBar = BossBar::findByType($player, $bossBarType);

            if ($bossBar === null) {
                $bossBar = new BossBar($player, $bossBarType, "", 0);
                $bossBar->send();
            }

            //制限時間がなかったら
            if ($timeLimit === null) {
                $bossBar->updateTitle("経過時間:" . $elapsedTime);
                continue;
            }

            $bossBar->updatePercentage($elapsedTime / $timeLimit);
            $bossBar->updateTitle($elapsedTime . "/" . $timeLimit);
        }
    }
}