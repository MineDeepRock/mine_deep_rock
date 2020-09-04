<?php


namespace mine_deep_rock\pmmp\form;


use form_builder\models\simple_form_elements\SimpleFormButton;
use form_builder\models\SimpleForm;
use mine_deep_rock\dao\PlayerEquipmentsDAO;
use mine_deep_rock\dao\PlayerStatusDAO;
use mine_deep_rock\model\MilitaryDepartment;
use mine_deep_rock\store\MilitaryDepartmentsStore;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SkillDepartmentListForm extends SimpleForm
{

    public function __construct(Player $player) {
        $buttons = [
            new SimpleFormButton(
                "汎用技能",
                null,
                function (Player $player) {
                    $player->sendForm(new SkillListForm($player, null));
                }
            )
        ];


        $equipments = PlayerEquipmentsDAO::get($player->getName());
        foreach (MilitaryDepartmentsStore::getAll() as $militaryDepartment) {
            if ($militaryDepartment->getName() === MilitaryDepartment::Sentry) continue;

            $text = $militaryDepartment->getNameJp() . "の専門技能";
            if ($equipments->getMilitaryDepartment()->getName() === $militaryDepartment->getName()) {
                $text = TextFormat::BOLD . $text;
            }

            $buttons[] = new SimpleFormButton(
                $text,
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