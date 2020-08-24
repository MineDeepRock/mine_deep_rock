<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\CustomForm;
use mine_deep_rock\service\SendOneOnOneRequestService;
use pocketmine\Player;
use team_game_system\model\Score;

class SendOneOnOneRequestForm extends CustomForm
{

    private $receiverName;

    private $timeLimit;
    private $maxScore;

    public function __construct(string $receiverName) {
        $this->receiverName = $receiverName;
        $this->maxScore = new Input("勝利判定スコア", "", "");
        $this->timeLimit = new Input("制限時間(秒)", "", "");

        parent::__construct("", [
            new Label("無い場合は空白でお願いします"),
            $this->maxScore,
            $this->timeLimit,
        ]);
    }

    function onSubmit(Player $player): void {
        $maxScore = $this->maxScore->getResult();
        $maxScore = $maxScore === "" ? null : new Score(intval($maxScore));

        $timeLimit = $this->timeLimit->getResult();
        $timeLimit = $timeLimit === "" ? null : intval($timeLimit);

        SendOneOnOneRequestService::execute($player->getName(), $this->receiverName, null, $maxScore, $timeLimit);
    }

    function onClickCloseButton(Player $player): void { }
}