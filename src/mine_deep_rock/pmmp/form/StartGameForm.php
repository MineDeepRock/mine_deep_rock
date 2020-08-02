<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\store\TDMGameIdsStore;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use team_game_system\model\GameId;
use team_game_system\TeamGameSystem;

class StartGameForm extends SimpleForm
{

    public function __construct(TaskScheduler $taskScheduler) {
        $buttons = array_map(function (GameId $gameId) use ($taskScheduler) {
            $game = TeamGameSystem::getGame($gameId);
            $map = $game->getMap();
            $participantsCount = count(TeamGameSystem::getGamePlayersData($gameId));
            return new SimpleFormButton(
                "TDM,Players:{$participantsCount},map:{$map->getName()}",
                null,
                function (Player $player) use ($game, $taskScheduler) {
                    $player->sendForm(new ConfirmStartGameForm($game, $taskScheduler));
                }
            );
        }, TDMGameIdsStore::getAll());

        parent::__construct("チームデスマッチ一覧", "開始させる試合を選んでください", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}