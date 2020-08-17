<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\model\DominationFlag;
use pocketmine\level\Level;
use pocketmine\level\particle\DustParticle;
use pocketmine\utils\Color;
use team_game_system\TeamGameSystem;

class SummonFlagParticlePMMPService
{
    static function execute(DominationFlag $flag, Level $level): void {
        $color = null;

        if ($flag->getGauge()->isEmpty()) {
            $color = new Color(255, 255, 255);
        } else if ($flag->getGauge()->isOccupied()) {
            $team = TeamGameSystem::getTeam($flag->getGameId(), $flag->getGauge()->getOccupyingTeamId());
            $color = TextFormatToColorPMMPService::execute($team->getTeamColorFormat());
        } else if ($flag->getGauge()->isOwned()) {
            $team = TeamGameSystem::getTeam($flag->getGameId(), $flag->getGauge()->getOwingTeamId());
            $color = TextFormatToColorPMMPService::execute($team->getTeamColorFormat());
        }

        if ($color === null) return;

        $level->addParticle(new DustParticle($flag->getPosition(), $color->getR(), $color->getG(), $color->getB()));
    }
}