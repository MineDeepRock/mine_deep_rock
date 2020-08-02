<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Dropdown;
use form_builder\models\custom_form_elements\Input;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use mine_deep_rock\pmmp\service\InformLobbyPlayersOpenGame;
use mine_deep_rock\service\CreateTDM;
use pocketmine\Player;
use team_game_system\model\Score;

class CreateGameForm extends CustomForm
{

    private $gameType;
    private $timeLimit;
    private $maxPlayersCount;
    private $maxScore;

    public function __construct() {
        $this->gameType = new Dropdown("GameType", ["TeamDeathMatch"]);
        $this->maxScore = new Input("勝利判定スコア", "", "");
        $this->maxPlayersCount = new Input("人数制限", "", "");
        $this->timeLimit = new Input("制限時間(秒)", "", "");

        parent::__construct("", [
            new Label("無い場合は空白でお願いします"),
            $this->gameType,
            $this->maxScore,
            $this->maxPlayersCount,
            $this->timeLimit,
        ]);
    }

    function onSubmit(Player $player): void {
        $gameType = $this->gameType->getResult();

        $maxScore = $this->maxScore->getResult();
        $maxScore = $maxScore === "" ? null : new Score(intval($maxScore));

        $maxPlayersCount = $this->maxPlayersCount->getResult();
        $maxPlayersCount = $maxPlayersCount === "" ? null : intval($maxPlayersCount);

        $timeLimit = $this->timeLimit->getResult();
        $timeLimit = $timeLimit === "" ? null : intval($timeLimit);


        if ($gameType === "TeamDeathMatch") {
            CreateTDM::execute($maxScore, $maxPlayersCount, $timeLimit);
        }

        InformLobbyPlayersOpenGame::execute($gameType);
    }

    function onClickCloseButton(Player $player): void { }
}