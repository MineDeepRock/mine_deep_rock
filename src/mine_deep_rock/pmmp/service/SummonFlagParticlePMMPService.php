<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\model\DominationFlag;
use pocketmine\level\Level;
use pocketmine\level\particle\DustParticle;
use pocketmine\math\Math;
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

        for ($i = 0; $i < 360; $i += 3) {
            $center = $flag->getPosition();

            $x = DominationFlag::Range * sin(deg2rad($i));
            $z = DominationFlag::Range * cos(deg2rad($i));

            $pos = $center->add($x, 0, $z);
            $gauge = $flag->getGauge();
            if ($gauge->isOwned() && !$gauge->isOccupied()) {
                if ($i % 2 === 1) {
                    $level->addParticle(new DustParticle($pos, 255, 255, 255));
                } else {
                    $level->addParticle(new DustParticle($pos, $color->getR(), $color->getG(), $color->getB()));
                }
            } else {
                $level->addParticle(new DustParticle($pos, $color->getR(), $color->getG(), $color->getB()));
            }
        }
    }
}