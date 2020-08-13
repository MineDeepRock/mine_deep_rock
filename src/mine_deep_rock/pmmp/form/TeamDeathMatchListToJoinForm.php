<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class TeamDeathMatchListToJoinForm extends SimpleForm
{

    public function __construct() {
        $buttons = array_map(function (GameId $gameId) {
            $game = TeamGameSystem::getGame($gameId);
            $map = $game->getMap();
            $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
            return new SimpleFormButton(
                "Players:" . TextFormat::BOLD . $participantsCount . TextFormat::RESET . ",map:" . TextFormat::BOLD . $map->getName(),
                null,
                function (Player $player) use ($gameId) {
                    $result = TeamGameSystem::joinGame($player, $gameId);

                    if ($result) {
                        $level = Server::getInstance()->getLevelByName("lobby");
                        $player->sendMessage("ゲームに参加しました");
                        foreach ($level->getPlayers() as $lobbyPlayer) {
                            $lobbyPlayer->sendMessage($player->getName() . "がTDMに参加しました");
                        }

                    } else {
                        $player->sendMessage("ゲームに参加出来ませんでした");
                    }
                }
            );
        }, TDMGameIdsStore::getAll());

        parent::__construct("チームデスマッチ一覧", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}