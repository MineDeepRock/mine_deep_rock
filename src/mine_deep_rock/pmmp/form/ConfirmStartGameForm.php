<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use team_game_system\model\Game;
use team_game_system\TeamGameSystem;

class ConfirmStartGameForm extends ModalForm
{
    private $scheduler;
    private $gameId;

    public function __construct(Game $game, TaskScheduler $taskScheduler) {
        $this->scheduler = $taskScheduler;
        $this->gameId = $game->getId();
        $map = $game->getMap();
        $participantsCount = count(TeamGameSystem::getGamePlayersData($game->getId()));

        parent::__construct("TDM,Players:{$participantsCount},map:{$map->getName()}", "", new ModalFormButton("開始させる"), new ModalFormButton("キャンセル"));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new StartGameForm($this->scheduler));
    }

    public function onClickButton1(Player $player): void {
        TeamGameSystem::startGame($this->scheduler, $this->gameId);
    }

    public function onClickButton2(Player $player): void {
        $player->sendForm(new StartGameForm($this->scheduler));
    }
}