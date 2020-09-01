<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Toggle;
use form_builder\models\CustomForm;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\Skill;
use mine_deep_rock\service\SetSkillsService;
use pocketmine\Player;

class SelectSkillsForm extends CustomForm
{
    /**
     * @var Toggle[]
     */
    private $list;


    public function __construct(Player $player) {
        $playerStatus = PlayerStatusDAO::get($player->getName());
        $this->list = [];

        foreach ($playerStatus->getOwningSkills() as $owningSkill) {
            if ($playerStatus->getMilitaryDepartment()->canEquipSkill($owningSkill)) {
                $default = false;
                if ($playerStatus->isSelectedSkill(Skill::fromString($owningSkill::Name))) {
                    $default = true;
                }
                $this->list[] = new Toggle($owningSkill::Name, $default);
            }
        }

        parent::__construct("専門技能の選択", $this->list);
    }

    function onSubmit(Player $player): void {
        $skills = [];
        $count = 0;
        foreach ($this->list as $toggle) {
            if ($toggle->getResult()) {
                $count++;
                if ($count > 3) {
                    return;
                }
                $skills[] = Skill::fromString($toggle->getText());
            }
        }

        SetSkillsService::execute($player->getName(), $skills);
    }

    function onClickCloseButton(Player $player): void { }
}