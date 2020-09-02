<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\Label;
use form_builder\models\custom_form_elements\Toggle;
use form_builder\models\CustomForm;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\Skill;
use mine_deep_rock\model\skill\normal\NormalSkill;
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
        $this->list = [
            new Label("専門技能")
        ];
        $normals = [
            new Label("汎用技能")
        ];

        foreach ($playerStatus->getOwningSkills() as $owningSkill) {
            $default = $playerStatus->isSelectedSkill($owningSkill);

            if ($owningSkill instanceof NormalSkill) {
                $normals[] = new Toggle($owningSkill::Name, $default);
                continue;
            }

            if ($playerStatus->getMilitaryDepartment()->canEquipSkill($owningSkill)) {
                $this->list[] = new Toggle($owningSkill::Name, $default);
                continue;
            }
        }

        $this->list = array_merge($this->list, $normals);

        parent::__construct("専門技能の選択", $this->list);
    }

    function onSubmit(Player $player): void {
        $skills = [];
        foreach ($this->list as $toggle) {
            if ($toggle->getResult()) {
                $skills[] = Skill::fromString($toggle->getText());
            }
        }

        if (count($skills) > 3) {
            $player->sendMessage("３つまでしか選べません");
        } else {
            SetSkillsService::execute($player->getName(), $skills);
        }
    }

    function onClickCloseButton(Player $player): void { }
}