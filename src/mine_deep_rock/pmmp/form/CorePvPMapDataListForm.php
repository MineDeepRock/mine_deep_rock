<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\CorePvPMapDataDAO;
use pocketmine\Player;

class CorePvPMapDataListForm extends SimpleForm
{
    public function __construct() {
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) {
                    $player->sendForm(new AddCorePvPMapDataForm());
                }
            ),
        ];

        foreach (CorePvPMapDataDAO::getRegisteredMapNames() as $name) {
            $buttons[] = new SimpleFormButton(
                $name,
                null,
                function (Player $player) use ($name) {
                    $player->sendForm(new CandidateCorePositionsGroupListForm($name));
                }
            );
        }
        parent::__construct("CorePvPMaps", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}