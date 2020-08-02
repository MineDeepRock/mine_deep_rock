<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\Player;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class TeamDeathMatchListForm extends SimpleForm
{

    public function __construct() {
        $buttons = array_map(function (GameId $gameId) {
            $game = TeamGameSystem::getGame($gameId);
            $map = $game->getMap();
            $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
            return new SimpleFormButton(
                "TDM,Players:{$participantsCount},map:{$map->getName()}",
                null,
                function (Player $player) use ($gameId) {
                    TeamGameSystem::joinGame($player, $gameId);
                }
            );
        }, TDMGameIdsStore::getAll());

        parent::__construct("チームデスマッチ一覧", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}