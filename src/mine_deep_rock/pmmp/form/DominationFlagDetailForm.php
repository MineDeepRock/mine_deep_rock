<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\DominationFlagDataDAO;
use mine_deep_rock\model\DominationFlagData;
use pocketmine\Player;

class DominationFlagDetailForm extends SimpleForm
{
    private $mapName;

    public function __construct(string $mapName, DominationFlagData $flagData) {
        $this->mapName = $mapName;

        $buttons = [
            new SimpleFormButton(
                "削除",
                null,
                function (Player $player) use ($mapName, $flagData) {
                    DominationFlagDataDAO::removeFlagData($mapName, $flagData);
                    $player->sendForm(new SettingDominationFlagForm($this->mapName));
                }
            ),
            new SimpleFormButton(
                "戻る",
                null,
                function (Player $player) {
                    $player->sendForm(new SettingDominationFlagForm($this->mapName));
                }
            )
        ];

        parent::__construct($mapName, "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new SettingDominationFlagForm($this->mapName));
    }
}