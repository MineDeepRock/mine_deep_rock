<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\SimpleForm;
use pocketmine\Player;

class SkillDepartmentListForm extends SimpleForm
{

    public function __construct() {
        parent::__construct("スキルを購入", "", [

        ]);
    }

    function onClickCloseButton(Player $player): void {
    }
}