<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\DominationFlagDataDAO;
use pocketmine\Player;

class DominationMapListForm extends SimpleForm
{
    public function __construct() {
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) {
                    $player->sendForm(new RegisterDominationMapForm());
                }
            )
        ];
        foreach (DominationFlagDataDAO::getRegisteredMapNames() as $name) {
            $buttons[] = new SimpleFormButton(
                $name,
                null,
                function (Player $player) use ($name) {
                    $player->sendForm(new SettingDominationFlagForm($name));
                }
            );
        }
        parent::__construct("ドミネーションのフラッグを設定", "", $buttons);
    }

    function onClickCloseButton(Player $player): void { }
}