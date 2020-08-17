<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\DominationFlagDataDAO;
use pocketmine\Player;

class SettingDominationFlagForm extends SimpleForm
{
    public function __construct(string $mapName) {
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) use ($mapName) {
                    $player->sendForm(new CreateDominationFlagDataForm($mapName));
                }
            )
        ];

        foreach (DominationFlagDataDAO::getFlagDataList($mapName) as $flagData) {
            $buttons[] = new SimpleFormButton(
                $flagData->getName(),
                null,
                function (Player $player) use ($mapName, $flagData) {
                    $player->sendForm(new DominationFlagDetailForm($mapName, $flagData));
                }
            );
        }
        parent::__construct($mapName, "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new DominationMapListForm());
    }
}