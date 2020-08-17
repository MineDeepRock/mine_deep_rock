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

class TDMListToJoinForm extends SimpleForm
{

    public function __construct() {
        $buttons = array_map(function (Game $game) {
            $gameId = $game->getId();
            $map = $game->getMap();
            $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
            return new SimpleFormButton(
                "Players:" . TextFormat::BOLD . $participantsCount . TextFormat::RESET . ",map:" . TextFormat::BOLD . $map->getName(),
                null,
                function (Player $player) use ($gameId) {

                    $result = TeamGameSystem::joinGame($player, $gameId);

                    if ($result) {
                        $level = Server::getInstance()->getLevelByName("lobby");
                        $player->sendMessage("TDMに参加しました");
                        foreach ($level->getPlayers() as $lobbyPlayer) {
                            $lobbyPlayer->sendMessage($player->getName() . "がTDMに参加しました");
                        }

                    } else {
                        $player->sendMessage("試合に参加出来ませんでした。\nすでに試合に参加しているか、試合が満員です");
                    }
                }
            );
        }, TeamGameSystem::findGamesByType(GameTypeList::TDM()));

        parent::__construct("チームデスマッチ一覧", "チームデスマッチに参加", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}