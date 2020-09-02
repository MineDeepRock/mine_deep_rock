<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\model\skill\normal\AntiSpot;
use mine_deep_rock\model\skill\normal\Cover;
use mine_deep_rock\model\skill\normal\Frack;
use mine_deep_rock\model\skill\normal\QuickRunAway;
use pocketmine\Player;

class SkillListForm extends SimpleForm
{
    public function __construct(Player $player, ?MilitaryDepartment $militaryDepartment) {
        $buttons = [];
        $playerStatus = PlayerStatusDAO::get($player->getName());
        if ($militaryDepartment === null) {
            $normals = [
                new AntiSpot(),
                new Cover(),
                new Frack(),
                new QuickRunAway(),
            ];
            foreach ($normals as $normalSkill) {
                $buttons[] = new SimpleFormButton(
                    $normalSkill::Name,
                    null,
                    function (Player $player) use ($normalSkill) {
                        $player->sendForm(new SkillDetailForm($normalSkill, null));
                    }
                );
            }
        } else {
            foreach ($militaryDepartment->getSkills() as $skill) {
                if (!$playerStatus->isOwingSkill($skill)) {
                    $buttons[] = new SimpleFormButton(
                        $skill::Name,
                        null,
                        function (Player $player) use ($skill, $militaryDepartment) {
                            $player->sendForm(new SkillDetailForm($skill, $militaryDepartment));
                        }
                    );
                }
            }
        }

        parent::__construct(
            "スキルを購入",
            $militaryDepartment === null ? "汎用技能" : $militaryDepartment->getName(),
            $buttons
        );
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new SkillDepartmentListForm());
    }
}