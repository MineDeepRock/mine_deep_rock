<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\service\AddCandidateCorePositionsGroupService;
use pocketmine\Player;

class CandidateCorePositionsGroupListForm extends SimpleForm
{
    public function __construct(string $mapName) {
        $buttons = [
            new SimpleFormButton(
                "追加",
                null,
                function (Player $player) use ($mapName) {
                    AddCandidateCorePositionsGroupService::execute($mapName);
                    $player->sendForm(new CandidateCorePositionsGroupListForm($mapName));
                }
            ),
        ];

        $mapData = CorePvPMapDataDAO::getMapData($mapName);
        foreach ($mapData->getCandidateCorePositionsGroups() as $index => $group) {
            $buttons[] = new SimpleFormButton(
                $index,
                null,
                function (Player $player) use ($mapName, $index, $group) {
                    $player->sendForm(new CandidateCorePositionsGroupForm($mapName, $index, $group));
                }
            );
        }

        parent::__construct($mapName, "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CorePvPMapDataListForm());
    }
}