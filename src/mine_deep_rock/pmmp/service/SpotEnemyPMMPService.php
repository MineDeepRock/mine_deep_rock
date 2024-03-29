<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\skill\normal\AntiSpot;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use team_game_system\TeamGameSystem;

class SpotEnemyPMMPService
{
    static function execute(Player $player, Player $enemy, float $tick, TaskScheduler $scheduler): void {
        $enemyData = TeamGameSystem::getPlayerData($enemy);
        $enemyEquipments = PlayerEquipmentsDAO::get($enemy->getName());
        ShowPrivateNameTagToParticipantsPMMPService::execute($enemy, $enemyData->getGameId());

        foreach ($enemyEquipments->getSelectedSkills() as $skill) {
            if ($skill instanceof AntiSpot) {
                $tick /= 2;
            }
        }

        $scheduler->scheduleDelayedTask(new ClosureTask(function (int $i) use ($enemy) : void {
            $enemy = Server::getInstance()->getPlayer($enemy->getName());
            if ($enemy === null) return;
            if ($enemy->isOnline()) {
                ShowPrivateNameTagToAllyPMMPService::execute($enemy);
            }
        }), $tick);

        $enemy->sendTip("スポットされました！３秒間相手に居場所がばれます！");
        //TODO:オーナーに経験値の処理
    }
}