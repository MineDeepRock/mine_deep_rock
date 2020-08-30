<?php


namespace mine_deep_rock\service;


use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class GenerateGameDescriptionService
{
    static function execute(Game $game, bool $forButtonText = false): string {
        $gameId = $game->getId();
        $map = $game->getMap();

        $maxScoreText = $game->getMaxScore() === null ? "無し" : $game->getMaxScore()->getValue();
        $timeLimitText = $game->getTimeLimit() === null ? "無し" : $game->getTimeLimit() . "秒";
        $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
        $participantsCountText = $game->getMaxPlayersCount() === null ? $participantsCount : "{$participantsCount}/{$game->getMaxPlayersCount()}";

        if ($forButtonText) {
            return "ゲームモード:" . TextFormat::BOLD . strval($game->getType()) . TextFormat::RESET .
                ",マップ:" . TextFormat::BOLD . $map->getName() . TextFormat::RESET .
                "\n勝利判定:" . TextFormat::BOLD . $maxScoreText . TextFormat::RESET .
                ",時間制限:" . TextFormat::BOLD . $timeLimitText . TextFormat::RESET .
                ",参加人数:" . TextFormat::BOLD . $participantsCountText . TextFormat::RESET;
        }

        return "ゲームモード:" . TextFormat::BOLD . strval($game->getType()) . TextFormat::RESET .
            "\nマップ:" . TextFormat::BOLD . $map->getName() . TextFormat::RESET .
            "\n勝利判定:" . TextFormat::BOLD . $maxScoreText . TextFormat::RESET .
            "\n時間制限:" . TextFormat::BOLD . $timeLimitText . TextFormat::RESET .
            "\n参加人数:" . TextFormat::BOLD . $participantsCountText . TextFormat::RESET;
    }
}