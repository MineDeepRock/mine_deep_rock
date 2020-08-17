<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use mine_deep_rock\dao\DominationFlagDataDAO;
use pocketmine\Player;

class RegisterDominationMapForm extends CustomForm
{
    private $mapNameElement;

    public function __construct() {
        $this->mapNameElement = new Input("", "", "");
        parent::__construct("マップを登録", [
            $this->mapNameElement
        ]);
    }

    function onSubmit(Player $player): void {
        DominationFlagDataDAO::registerMap($this->mapNameElement->getResult());
        $player->sendForm(new DominationMapListForm());
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new DominationMapListForm());
    }
}