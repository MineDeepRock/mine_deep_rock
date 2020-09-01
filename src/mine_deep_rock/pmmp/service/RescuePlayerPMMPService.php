<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\model\skill\assault_soldier\SecondChance;
use mine_deep_rock\model\skill\nursing_soldier\StimulantSyringe;
use mine_deep_rock\service\UpdatePlayerGameStatusIsResuscitated;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;
use team_game_system\TeamGameSystem;

class RescuePlayerPMMPService
{
    static function execute(Player $player, Player $target): void {
        $playerData = TeamGameSystem::getPlayerData($player);
        $targetData = TeamGameSystem::getPlayerData($target);
        if ($playerData->getTeamId() === null || $targetData->getTeamId() === null) return;

        $playerGameStatus = PlayerGameStatusStore::findByName($target->getName());
        if ($playerGameStatus->isResuscitated()) return;

        if ($playerData->getTeamId()->equals($targetData->getTeamId())) {
            $playerStatus = PlayerStatusDAO::get($player->getName());
            if ($playerStatus->getMilitaryDepartment()->getName() === MilitaryDepartment::NursingSoldier) {
                UpdatePlayerGameStatusIsResuscitated::execute($target->getName());
                ResortPMMPService::execute($target, $target->getPosition(), true);

                $health = 10;

                $targetStatus = PlayerStatusDAO::get($target->getName());
                if ($targetStatus->isSelectedSkill(new SecondChance())) {
                    $target->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), null, 1, false));
                }

                if($playerStatus->isSelectedSkill(new StimulantSyringe())) {
                    $health = 16;
                }

                $target->setHealth($health);

                $player->sendMessage($target->getName() . "を蘇生した");
                $target->sendMessage($player->getName() . "に蘇生されました");
            }
        }
    }
}