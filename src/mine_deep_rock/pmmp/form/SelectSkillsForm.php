<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\custom_form_elements\CustomFormElement;
use form_builder\models\custom_form_elements\Label;
use form_builder\models\custom_form_elements\Toggle;
use form_builder\models\CustomForm;
use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\Skill;
use mine_deep_rock\model\skill\normal\NormalSkill;
use mine_deep_rock\pmmp\service\PlaySoundPMMPService;
use mine_deep_rock\pmmp\SoundNameList;
use mine_deep_rock\service\SetSkillsService;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SelectSkillsForm extends CustomForm
{
    /**
     * @var CustomFormElement[]
     */
    private $list;


    public function __construct(Player $player) {
        $playerStatus = PlayerStatusDAO::get($player->getName());
        $equipments = PlayerEquipmentsDAO::get($player->getName());
        $this->list = [
            new Label(TextFormat::BOLD . "専門技能")
        ];
        $normals = [
            new Label(TextFormat::BOLD . "汎用技能")
        ];

        foreach ($playerStatus->getOwningSkills() as $owningSkill) {
            $isSelected = $equipments->isSelectedSkill($owningSkill);
            $format = $isSelected ? TextFormat::GREEN : TextFormat::RESET;

            if ($owningSkill instanceof NormalSkill) {
                $normals[] = new Label($format . $owningSkill::Name . ":" . TextFormat::RESET . $owningSkill::Description);
                $normals[] = new Toggle("", $isSelected);
                continue;
            }

            if ($equipments->getMilitaryDepartment()->canEquipSkill($owningSkill)) {
                $this->list[] = new Label($format . $owningSkill::Name . ":" . TextFormat::RESET . $owningSkill::Description);
                $this->list[] = new Toggle("", $isSelected);
                continue;
            }
        }

        $this->list = array_merge($normals, $this->list);

        parent::__construct("専門技能の選択", $this->list);
    }

    function onSubmit(Player $player): void {
        $skills = [];
        foreach ($this->list as $index => $toggle) {
            if ($toggle instanceof Toggle) {
                if ($toggle->getResult()) {
                    $name = $this->list[$index-1]->getText();
                    $name = explode(":", $name)[0];
                    $name = str_replace(TextFormat::GREEN, "", $name);
                    $name = str_replace(TextFormat::RESET, "", $name);

                    $skills[] = Skill::fromString($name);
                }
            }
        }

        if (count($skills) > 3) {
            $player->sendMessage("３つまでしか選べません");
            PlaySoundPMMPService::execute($player, $player, SoundNameList::Failure);
        } else {
            SetSkillsService::execute($player->getName(), $skills);
        }
    }

    function onClickCloseButton(Player $player): void { }
}