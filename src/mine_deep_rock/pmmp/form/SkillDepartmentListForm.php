<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\store\MilitaryDepartmentsStore;
use pocketmine\Player;

class SkillDepartmentListForm extends SimpleForm
{

    public function __construct() {
        $buttons = [
            new SimpleFormButton(
                "汎用技能",
                null,
                function (Player $player) {
                    $player->sendForm(new SkillListForm($player, null));
                }
            )
        ];
        foreach (MilitaryDepartmentsStore::getAll() as $militaryDepartment) {
            $buttons[] = new SimpleFormButton(
                $militaryDepartment->getName(),
                null,
                function (Player $player) use ($militaryDepartment) {
                    $player->sendForm(new SkillListForm($player, $militaryDepartment));
                }
            );
        }


        parent::__construct("スキルを購入", "", $buttons);
    }

    function onClickCloseButton(Player $player): void {
    }
}