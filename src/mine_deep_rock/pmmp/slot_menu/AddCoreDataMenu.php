<?php


namespace mine_deep_rock\pmmp\slot_menu;


use mine_deep_rock\data_model\CoreData;
use mine_deep_rock\pmmp\form\CoreDataListForm;
use mine_deep_rock\service\AddCoreDataService;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\Player;
use slot_menu_system\SlotMenu;
use slot_menu_system\SlotMenuElement;
use slot_menu_system\SlotMenuSystem;

class AddCoreDataMenu extends SlotMenu
{
    public function __construct(string $mapName, string $teamColor) {
        $menus = [
            new SlotMenuElement(ItemIds::END_STONE, "追加", function () { }, function (Player $player, Block $block) use ($mapName, $teamColor) {
                AddCoreDataService::execute($mapName, new CoreData($teamColor, $block));
                SlotMenuSystem::close($player);
                $player->sendForm(new CoreDataListForm($mapName));
            }),
        ];
        parent::__construct($menus);
    }
}