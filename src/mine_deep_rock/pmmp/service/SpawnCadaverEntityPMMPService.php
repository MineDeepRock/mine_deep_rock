<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\pmmp\entity\CadaverEntity;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;
use team_game_system\TeamGameSystem;

class SpawnCadaverEntityPMMPService
{
    static function execute(Player $victim, TaskScheduler $scheduler): void {
        $victimGameStatus = PlayerGameStatusStore::findByName($victim->getName());
        $playerData = TeamGameSystem::getPlayerData($victim);
        if ($playerData->getTeamId() === null) return;
        $team = TeamGameSystem::getTeam($playerData->getGameId(),$playerData->getTeamId());

        if ($victimGameStatus->isResuscitated()) {
            $nameTag = $team->getTeamColorFormat() . "[蘇生不可能]" . $victim->getName();
        } else {
            $nameTag = $team->getTeamColorFormat() . "[蘇生可能]" . $victim->getName();
        }

        $cadaverEntity = new CadaverEntity($victim->getLevel(), $victim, $scheduler);
        $cadaverEntity->setNameTag($nameTag);
        $cadaverEntity->setNameTagAlwaysVisible(true);
        $cadaverEntity->spawnToAll();
    }
}