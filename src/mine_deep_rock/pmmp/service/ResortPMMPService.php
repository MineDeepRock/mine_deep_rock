<?php


namespace mine_deep_rock\pmmp\service;


use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\model\PlayerGameStatus;
use mine_deep_rock\pmmp\event\PlayerResortedEvent;
use mine_deep_rock\service\SelectMilitaryDepartmentService;
use mine_deep_rock\service\UpdatePlayerGameStatusIsResuscitated;
use mine_deep_rock\store\PlayerGameStatusStore;
use pocketmine\level\Position;
use pocketmine\Player;
use team_game_system\TeamGameSystem;

class ResortPMMPService
{
    static function execute(Player $player, Position $pos = null, bool $byRescue = false): void {
        $playerData = TeamGameSystem::getPlayerData($player);
        if ($playerData->getTeamId() === null) {
            return;
        }

        //SentryだったらAssaultSoldierに戻す
        $equipments = PlayerEquipmentsDAO::get($playerData->getName());
        if ($equipments->getMilitaryDepartment()->getName() === MilitaryDepartment::Sentry) {
            SelectMilitaryDepartmentService::execute($player->getName(), MilitaryDepartment::AssaultSoldier);
        }

        $player->setGamemode(Player::ADVENTURE);
        $player->setImmobile(false);

        $game = TeamGameSystem::getGame($playerData->getGameId());
        if ($game === null) return;

        if ($pos !== null) {
            $player->teleport($pos);
        } else {
            TeamGameSystem::setSpawnPoint($player);
            $player->teleport($player->getSpawn());

            $playerGameStatus = PlayerGameStatusStore::findByName($player->getName());
            PlayerGameStatusStore::update(new PlayerGameStatus(
                $playerGameStatus->getName(),
                false,
                $playerGameStatus->getKillCountInGame()
            ));
        }

        RemoveCadaverEntityPMMPService::execute($player);

        InitEquipmentsPMMPService::execute($player);

        InitEffectsPMMPService::execute($player);

        ShowPrivateNameTagToAllyPMMPService::execute($player);

        $event = new PlayerResortedEvent($player, $byRescue);
        $event->call();
    }
}