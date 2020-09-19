<?php


namespace mine_deep_rock\service;


use Exception;
use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\GameTypeList;
use mine_deep_rock\model\Core;
use mine_deep_rock\pmmp\block\CoreBlock;
use mine_deep_rock\store\CoresStore;
use pocketmine\level\Position;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\model\Score;
use team_game_system\model\Team;
use team_game_system\TeamGameSystem;

class CreateCorePvPGameService
{
    static function execute(string $mapName, int $coreHealth, ?int $maxPlayersCount = null): void {
        /** @var Team[] $teams */
        $teams = [
            Team::asNew("Red", TextFormat::RED),
            Team::asNew("Blue", TextFormat::BLUE),
        ];

        try {
            $map = TeamGameSystem::selectMap($mapName, $teams);

            $game = Game::asNew(GameTypeList::CorePvP(), $map, $teams);
            $game->setMaxScore(new Score(1));
            $game->setMaxPlayersCount($maxPlayersCount);
            $game->setMaxPlayersDifference(1);

            Server::getInstance()->loadLevel($map->getLevelName());
            $level = Server::getInstance()->getLevelByName($map->getLevelName());

            foreach (CorePvPMapDataDAO::getMapData($mapName)->getCoreDataList() as $coreData) {
                foreach ($game->getTeams() as $team) {
                    if ($team->getTeamColorFormat() === $coreData->getTeamColor()) {
                        $pos = Position::fromObject($coreData->getCoordinate(), $level);
                        $level->setBlock($pos, new CoreBlock());

                        CoresStore::add(new Core(
                            $game->getId(),
                            $team->getId(),
                            $pos,
                            $coreHealth
                        ));
                    }
                }
            }

            TeamGameSystem::registerGame($game);
        } catch (Exception $e) {
            Server::getInstance()->getLogger()->info("{$mapName}は存在しません");
        }
    }
}