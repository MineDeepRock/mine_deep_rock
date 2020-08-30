<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\GameTypeList;
use mine_deep_rock\service\GenerateGameDescriptionService;
use pocketmine\Player;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class GameListToJoinForm extends SimpleForm
{

    public function __construct() {


        $buttons = [];
        /** @var Game $game */
        foreach (TeamGameSystem::getAllGames() as $game) {
            if ($game->getType()->equals(GameTypeList::OneOnOne())) continue;

            $buttons[] = new SimpleFormButton(
                GenerateGameDescriptionService::execute($game, true),
                null,
                function (Player $player) use ($game) {
                    $player->sendForm(new GameDetailToJoinForm($game));
                }
            );
        }

        parent::__construct("ゲーム一覧", "ゲームに参加", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}