<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\data_model\CoreData;
use mine_deep_rock\pmmp\service\ColorTextToString;
use mine_deep_rock\service\DeleteCoreDataService;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CoreDataDetailForm extends SimpleForm
{
    private $mapName;

    public function __construct(string $mapName, CoreData $coreData) {
        $this->mapName = $mapName;

        $buttons = [
            new SimpleFormButton(
                TextFormat::RED . "削除",
                null,
                function (Player $player) use ($mapName, $coreData) {
                    DeleteCoreDataService::execute($mapName, $coreData);
                    $player->sendForm(new CoreDataListForm($this->mapName));
                }
            ),
        ];


        $pos = $coreData->getCoordinate();
        parent::__construct(
            $coreData->getTeamColor() . ColorTextToString::execute($coreData->getTeamColor()),
            intval($pos->getX()) . "," . intval($pos->getY()) . "," . intval($pos->getZ()),
            $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CoreDataListForm($this->mapName));
    }
}