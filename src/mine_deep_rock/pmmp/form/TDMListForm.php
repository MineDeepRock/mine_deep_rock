<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class TDMListForm extends SimpleForm
{

    public function __construct() {
        $buttons = array_map(function (GameId $gameId) {
            $game = TeamGameSystem::getGame($gameId);
            $map = $game->getMap();
            $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
            return new SimpleFormButton(
                "Players:" . TextFormat::BOLD . $participantsCount . TextFormat::RESET . ",map:" . TextFormat::BOLD . $map->getName(),
                null,
                function (Player $player) use ($game) {
                    $player->sendForm(new ParticipantsListForm($game));
                }
            );
        }, TDMGameIdsStore::getAll());

        parent::__construct("チームデスマッチ一覧", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}