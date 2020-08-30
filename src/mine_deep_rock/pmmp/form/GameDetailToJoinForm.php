<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\model\GameType;
use team_game_system\TeamGameSystem;

class GameDetailToJoinForm extends SimpleForm
{

    public function __construct(Game $game) {
        $gameId = $game->getId();
        $map = $game->getMap();

        $maxScoreText = $game->getMaxScore() === null ? "無し" : $game->getMaxScore()->getValue();
        $timeLimitText = $game->getTimeLimit() === null ? "無し" : $game->getTimeLimit() . "秒";
        $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
        $participantsCountText = $game->getMaxPlayersCount() === null ? $participantsCount : "{$participantsCount}/{$game->getMaxPlayersCount()}";

        $buttons = [];

        $onJoin = function (Player $player, GameType $gameTyp, bool $result) {
            if ($result) {
                $level = Server::getInstance()->getLevelByName("lobby");
                $player->sendMessage(strval($gameTyp) . "に参加しました");
                foreach ($level->getPlayers() as $lobbyPlayer) {
                    $lobbyPlayer->sendMessage($player->getName() . "が" . strval($gameTyp) . "に参加しました");
                }

            } else {
                $player->sendMessage("試合に参加出来ませんでした。\nすでに試合に参加しているか、試合が満員です");
            }
        };

        foreach ($game->getTeams() as $team) {
            $buttons[] = new SimpleFormButton(
                $team->getTeamColorFormat() . $team->getName(),
                null,
                function (Player $player) use ($game, $onJoin, $team) {
                    $result = TeamGameSystem::joinGame($player, $game->getId(), $team->getId());
                    $onJoin($player, $game->getType(), $result);
                }
            );
        }

        $buttons[] = new SimpleFormButton(
            "ランダム",
            null,
            function (Player $player) use ($game, $onJoin) {
                $result = TeamGameSystem::joinGame($player, $game->getId());
                $onJoin($player, $game->getType(), $result);
            }
        );

        parent::__construct(
            "ゲームに参加",
            "\nゲームモード:" . TextFormat::BOLD . strval($game->getType()) . TextFormat::RESET .
            "\nマップ:" . TextFormat::BOLD . $map->getName() . TextFormat::RESET .
            "\n勝利判定:" . TextFormat::BOLD . $maxScoreText . TextFormat::RESET .
            "\n時間制限:" . TextFormat::BOLD . $timeLimitText . TextFormat::RESET .
            "\n参加人数:" . TextFormat::BOLD . $participantsCountText . TextFormat::RESET
            , $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new GameListToJoinForm());
    }
}