<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\GameTypeList;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class GameListToJoinForm extends SimpleForm
{

    public function __construct() {


        $buttons = [];
        /** @var Game $game */
        foreach (TeamGameSystem::getAllGames() as $game) {
            if ($game->getType()->equals(GameTypeList::OneOnOne())) continue;

            $gameId = $game->getId();
            $map = $game->getMap();

            $maxScoreText = $game->getMaxScore() === null ? "無し" : $game->getMaxScore()->getValue();
            $timeLimitText = $game->getTimeLimit() === null ? "無し" : $game->getTimeLimit() . "秒";
            $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
            $participantsCountText = $game->getMaxPlayersCount() === null ? $participantsCount : "{$participantsCount}/{$game->getMaxPlayersCount()}";
            $buttons[] = new SimpleFormButton(
                "ゲームモード:" . TextFormat::BOLD . strval($game->getType()) . TextFormat::RESET .
                ",マップ:" . TextFormat::BOLD . $map->getName() . TextFormat::RESET .
                "\n勝利判定:" . TextFormat::BOLD . $maxScoreText . TextFormat::RESET .
                ",時間制限:" . TextFormat::BOLD . $timeLimitText . TextFormat::RESET .
                ",参加人数:" . TextFormat::BOLD . $participantsCountText . TextFormat::RESET ,
                null,
                function (Player $player) use ($game, $gameId) {

                    $result = TeamGameSystem::joinGame($player, $gameId);

                    if ($result) {
                        $level = Server::getInstance()->getLevelByName("lobby");
                        $player->sendMessage(strval($game->getType()) . "に参加しました");
                        foreach ($level->getPlayers() as $lobbyPlayer) {
                            $lobbyPlayer->sendMessage($player->getName() . "が" . strval($game->getType()) . "に参加しました");
                        }

                    } else {
                        $player->sendMessage("試合に参加出来ませんでした。\nすでに試合に参加しているか、試合が満員です");
                    }
                }
            );
        }

        parent::__construct("ゲーム一覧", "ゲームに参加", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}