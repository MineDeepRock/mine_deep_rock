<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class GameListToJoinForm extends SimpleForm
{

    public function __construct() {
        $buttons = array_map(function (Game $game) {
            $gameId = $game->getId();
            $map = $game->getMap();
            $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
            return new SimpleFormButton(
                "Players:" . TextFormat::BOLD . $participantsCount . TextFormat::RESET . ",map:" . TextFormat::BOLD . $map->getName(),
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
        }, TeamGameSystem::getAllGames());

        parent::__construct("ゲーム一覧", "ゲームに参加", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}