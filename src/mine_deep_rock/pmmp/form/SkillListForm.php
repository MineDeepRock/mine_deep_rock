<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use pocketmine\Player;

class SkillListForm extends SimpleForm
{
    public function __construct(Player $player, MilitaryDepartment $militaryDepartment) {
        $buttons = [];
        $playerStatus = PlayerStatusDAO::get($player->getName());
        foreach ($militaryDepartment->getSkills() as $skill) {
            if (!$playerStatus->isOwingSkill($skill)) {
                $buttons[] = new SimpleFormButton(
                    $skill::Name,
                    null,
                    function (Player $player) use ($skill, $militaryDepartment) {
                        $player->sendForm(new SkillDetailForm($skill, $militaryDepartment));
                    }
                );
                break;
            }
        }
        parent::__construct(
            "スキルを購入",
            $militaryDepartment->getName(),
            $buttons
        );
    }

    function onClickCloseButton(Player $player): void {
    }
}