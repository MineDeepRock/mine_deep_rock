<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\data_model\CandidateCorePositionsGroup;
use mine_deep_rock\service\AddCandidateCorePositionService;
use mine_deep_rock\service\DeleteCandidateCorePositionsGroup;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CandidateCorePositionsGroupForm extends SimpleForm
{
    private $mapName;

    public function __construct(string $mapName, int $index, CandidateCorePositionsGroup $group) {
        $this->mapName = $mapName;

        $buttons = [
            new SimpleFormButton(
                TextFormat::GREEN . "追加",
                null,
                function (Player $player) use ($mapName, $index) {
                    AddCandidateCorePositionService::execute($mapName, $index, $player->getPosition());

                    $group = CorePvPMapDataDAO::getMapData($mapName)->getCandidateCorePositionsGroups()[$index];
                    $player->sendForm(new CandidateCorePositionsGroupForm($mapName, $index, $group));
                }
            )
        ];
        foreach ($group->getPositions() as $position) {
            $buttons[] = new SimpleFormButton(
                intval($position->getX()) . "," . intval($position->getY()) . "," . intval($position->getZ()),
                null,
                function (Player $player) use ($mapName, $index, $group, $position) {
                    $player->sendForm(new SettingCandidateCorePositionForm($mapName, $index, $group, $position));
                }
            );
        }

        $buttons[] = new SimpleFormButton(
            TextFormat::RED . "グループを削除",
            null,
            function (Player $player) use ($mapName, $index) {
                DeleteCandidateCorePositionsGroup::execute($mapName, $index);
                $player->sendForm(new CandidateCorePositionsGroupListForm($this->mapName));
            }
        );

        parent::__construct($index, "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CandidateCorePositionsGroupListForm($this->mapName));
    }
}