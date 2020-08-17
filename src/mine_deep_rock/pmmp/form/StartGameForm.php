<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class StartGameForm extends SimpleForm
{

    public function __construct(TaskScheduler $taskScheduler) {
        $buttons = array_map(function (Game $game) use ($taskScheduler) {
            $map = $game->getMap();
            $participantsCount = count(TeamGameSystem::getGamePlayersData($game->getId()));
            return new SimpleFormButton(
                "TDM,Players:{$participantsCount},map:{$map->getName()}",
                null,
                function (Player $player) use ($game, $taskScheduler) {
                    $player->sendForm(new ConfirmStartGameForm($game, $taskScheduler));
                }
            );
        }, TeamGameSystem::getAllGames());

        parent::__construct("チームデスマッチ一覧", "開始させる試合を選んでください", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}