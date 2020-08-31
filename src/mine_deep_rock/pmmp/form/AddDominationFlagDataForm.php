<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Input;
use form_builder\models\CustomForm;
use mine_deep_rock\dao\DominationFlagDataDAO;
use mine_deep_rock\model\DominationFlagData;
use pocketmine\Player;

class AddDominationFlagDataForm extends CustomForm
{
    private $mapName;

    private $nameElement;
    private $rangeElement;

    public function __construct(string $mapName) {
        $this->mapName = $mapName;

        $this->nameElement = new Input("拠点名", "", "");
        $this->rangeElement = new Input("直径", "", "5");
        parent::__construct($mapName, [
            $this->nameElement,
            $this->rangeElement
        ]);
    }

    function onSubmit(Player $player): void {
        DominationFlagDataDAO::addFlagData(
            $this->mapName,
            new DominationFlagData($this->nameElement->getResult(), $player->getPosition(), intval($this->rangeElement->getResult()))
        );
        $player->sendForm(new SettingDominationFlagForm($this->mapName));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new SettingDominationFlagForm($this->mapName));
    }
}