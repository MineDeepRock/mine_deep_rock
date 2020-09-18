<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\CorePvPMapDataDAO;
use mine_deep_rock\pmmp\service\ColorTextToString;
use mine_deep_rock\pmmp\slot_menu\AddCoreDataMenu;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use slot_menu_system\SlotMenuSystem;

class CoreDataListForm extends SimpleForm
{
    public function __construct(string $mapName) {

        //TODO:2チームしか想定していない
        $buttons = [
            new SimpleFormButton(
                TextFormat::RED . "REDを追加",
                null,
                function (Player $player) use ($mapName) {
                    SlotMenuSystem::send($player, new AddCoreDataMenu($mapName, TextFormat::RED));
                }
            ),
            new SimpleFormButton(
                TextFormat::BLUE . "BLUEを追加",
                null,
                function (Player $player) use ($mapName) {
                    SlotMenuSystem::send($player, new AddCoreDataMenu($mapName, TextFormat::BLUE));
                }
            ),
        ];

        $mapData = CorePvPMapDataDAO::getMapData($mapName);
        foreach ($mapData->getCoreDataList() as $index => $coreData) {
            $buttons[] = new SimpleFormButton(
                $coreData->getTeamColor() . ColorTextToString::execute($coreData->getTeamColor()),
                null,
                function (Player $player) use ($mapName, $coreData) {
                    $player->sendForm(new CoreDataDetailForm($mapName, $coreData));
                }
            );
        }

        parent::__construct($mapName, "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new CorePvPMapDataListForm());
    }
}