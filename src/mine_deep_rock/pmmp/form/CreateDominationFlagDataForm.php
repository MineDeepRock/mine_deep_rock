<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use mine_deep_rock\dao\DominationFlagDataDAO;
use mine_deep_rock\model\DominationFlagData;
use pocketmine\Player;

class CreateDominationFlagDataForm extends CustomForm
{
    private $mapName;

    private $nameElement;

    public function __construct(string $mapName) {
        $this->mapName = $mapName;

        $this->nameElement = new Input("", "", "");
        parent::__construct($mapName, [
            $this->nameElement
        ]);
    }

    function onSubmit(Player $player): void {
        DominationFlagDataDAO::addFlagData(
            $this->mapName,
            new DominationFlagData($this->nameElement->getResult(), $player->getPosition())
        );
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new SettingDominationFlagForm($this->mapName));
    }
}