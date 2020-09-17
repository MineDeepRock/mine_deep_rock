<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\data_model\CandidateCorePositionsGroup;
use mine_deep_rock\service\DeleteCandidateCorePositionService;
use pocketmine\level\Position;
use pocketmine\Player;

class SettingCandidateCorePositionForm extends SimpleForm
{
    private $mapName;
    private $index;
    private $group;

    public function __construct(string $mapName, int $index, CandidateCorePositionsGroup $group, Position $position) {
        $this->mapName = $mapName;
        $this->index = $index;;
        $this->group = $group;

        $buttons = [
            new SimpleFormButton(
                "削除",
                null,
                function (Player $player) use ($position) {
                    DeleteCandidateCorePositionService::execute($this->mapName, $this->index, $position);
                }
            ),
        ];

        parent::__construct($position->getX() . "," . $position->getY() . "," . $position->getZ(), "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CandidateCorePositionsGroupForm($this->index, $this->group));
    }
}