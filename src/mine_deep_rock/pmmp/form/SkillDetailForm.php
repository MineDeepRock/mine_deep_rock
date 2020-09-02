<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\modal_form_elements\ModalFormButton;
use form_builder\models\ModalForm;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\model\Skill;
use mine_deep_rock\service\BuySkillService;
use pocketmine\Player;

class SkillDetailForm extends ModalForm
{
    private $skill;
    private $militaryDepartment;

    public function __construct(Skill $skill, ?MilitaryDepartment $militaryDepartment) {
        $this->skill = $skill;
        $this->militaryDepartment = $militaryDepartment;
        parent::__construct($skill::Name, $skill::Description, new ModalFormButton("購入"), new ModalFormButton("キャンセル"));
    }

    function onClickCloseButton(Player $player): void {
        $player->sendForm(new SkillListForm($player, $this->militaryDepartment));
    }

    public function onClickButton1(Player $player): void {
        $result = BuySkillService::execute($player->getName(), $this->skill);
        if ($result) {
            $player->sendMessage($this->skill::Name . " を購入しました");
        } else {
            $player->sendMessage("所持金が不足しているか、すでに所有しています");
        }
    }

    public function onClickButton2(Player $player): void {
        $player->sendForm(new SkillListForm($player, $this->militaryDepartment));
    }
}